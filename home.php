<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="home.css">
    <title>Home</title>
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }

        .card {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ccc;
            cursor: pointer;
        }

        .card:hover {
            background-color: #f0f0f0;
        }
        
        .close {
            color: #aaa;
            float: right;
            font-size: 30px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 10px;
        }
        
        .pagination button {
            margin: 0 5px;
            cursor: pointer;
        }
        
        .pagination input[type="number"] {
            width: 50px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div id="user">
        <a href="register.php">
            <button>
                Pas de compte ?
            </button>
        </a>
        <br><br>
        <a href="login.php">
            <button>
                Déjà un compte ?
            </button>
        </a>
        <br><br>
        <a href="logout.php">
            <img src="img/logout.png" alt="Logout" class="logout-button">
        </a>
    </div>
    <button id="popup-button">Afficher les Joueurs</button>
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <input type="text" id="player-search" placeholder="Rechercher un joueur">
            <button onclick="searchPlayers()">Recherche</button>
            <button onclick="resetPlayerSearch()">Réinitialiser la recherche</button>
            <ul id="player-list"></ul>
            <div class="pagination" id="player-pagination"></div>
            <div>
                Aller à la page : <input type="number" id="player-page-number" min="1" value="1">
                <button onclick="goToPlayerPage()">Go</button>
            </div>
        </div>
    </div>
    <button id="team-popup-button">Afficher les Équipes</button>
    <div id="teamModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <input type="text" id="team-search" placeholder="Rechercher une équipe">
            <button onclick="searchTeams()">Recherche</button>
            <button onclick="resetTeamSearch()">Réinitialiser la recherche</button>
            <ul id="team-list"></ul>
            <div class="pagination" id="team-pagination"></div>
        </div>
    </div>
    <button id="match-popup-button">Afficher les Matchs</button>
    <div id="matchModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <ul id="match-list"></ul>
            <div class="pagination" id="match-pagination"></div>
            <div>
                Aller à la page : <input type="number" id="match-page-number" min="1" value="1">
                <button onclick="goToMatchPage()">Go</button>
            </div>
        </div>
    </div>
    <script>
        let currentPlayerPage = 1;
        let currentTeamPage = 1;
        let currentMatchPage = 1;

        let allTeams = [];

        function fetchAllTeams() {
            fetch("https://www.balldontlie.io/api/v1/teams")
                .then(response => response.json())
                .then(data => {
                    allTeams = data.data;
                })
                .catch(error => {
                    console.log('Erreur lors de la récupération de toutes les équipes:', error);
                });
        }

        function searchTeamsLocally(searchTerm) {
            searchTerm = searchTerm.trim().toLowerCase();
            const filteredTeams = allTeams.filter(team => team.full_name.toLowerCase().includes(searchTerm));
            return filteredTeams;
        }

        function fetchPlayers(page) {
            const searchTerm = document.getElementById('player-search').value.trim().toLowerCase();
            let apiUrl = `https://www.balldontlie.io/api/v1/players?page=${page}`;
            if (searchTerm !== "") {
                apiUrl += `&search=${searchTerm}`;
            }

            fetch(apiUrl)
                .then(response => response.json())
                .then(data => {
                    const playerList = document.getElementById('player-list');
                    playerList.innerHTML = "";

                    data.data.forEach(player => {
                        const listItem = document.createElement('li');
                        const card = document.createElement('div');
                        card.classList.add('card');
                        card.textContent = `${player.first_name} ${player.last_name}`;
                        card.addEventListener('click', function() {
                            window.location.href = `player.php?id=${player.id}`;
                        });
                        listItem.appendChild(card);
                        playerList.appendChild(listItem);
                    });

                    updatePlayerPagination(page, data.meta.total_pages);
                })
                .catch(error => {
                    console.log('Erreur lors de la récupération des données des joueurs:', error);
                });
        }

        function fetchTeams(page) {
            const searchTerm = document.getElementById('team-search').value.trim().toLowerCase();
            let teamsToDisplay = allTeams;

            if (searchTerm !== "") {
                teamsToDisplay = searchTeamsLocally(searchTerm);
            }

            const teamsPerPage = 25;
            const startIndex = (page - 1) * teamsPerPage;
            const endIndex = startIndex + teamsPerPage;
            const teamsOnPage = teamsToDisplay.slice(startIndex, endIndex);

            const teamList = document.getElementById('team-list');
            teamList.innerHTML = "";

            teamsOnPage.forEach(team => {
                const listItem = document.createElement('li');
                const card = document.createElement('div');
                card.classList.add('card');
                card.textContent = `${team.full_name}`;
                card.addEventListener('click', function() {
                    window.location.href = `team.php?id=${team.id}`;
                });
                listItem.appendChild(card);
                teamList.appendChild(listItem);
            });

            updateTeamPagination(page, Math.ceil(teamsToDisplay.length / teamsPerPage));
        }

        function fetchMatches(page) {
            fetch(`https://www.balldontlie.io/api/v1/games?page=${page}`)
                .then(response => response.json())
                .then(data => {
                    const matchList = document.getElementById('match-list');
                    matchList.innerHTML = "";

                    data.data.forEach(match => {
                        const listItem = document.createElement('li');
                        const card = document.createElement('div');
                        card.classList.add('card');
                        const date = new Date(match.date);
                        const formattedDate = `${date.getDate()}/${date.getMonth() + 1}/${date.getFullYear()}`;
                        card.textContent = `${match.home_team.abbreviation} VS ${match.visitor_team.abbreviation} - ${formattedDate}`;
                        card.addEventListener('click', function() {
                            window.location.href = `match.php?id=${match.id}`;
                        });
                        listItem.appendChild(card);
                        matchList.appendChild(listItem);
                    });

                    updateMatchPagination(page, data.meta.total_pages);
                })
                .catch(error => {
                    console.log('Erreur lors de la récupération des données des matchs:', error);
                });
        }

        function updatePlayerPagination(currentPage, totalPages) {
            const pagination = document.getElementById('player-pagination');
            pagination.innerHTML = "";

            const maxPagesToShow = 5;
            const halfMaxPages = Math.floor(maxPagesToShow / 2);

            if (currentPage > 1) {
                const prevButton = document.createElement('button');
                prevButton.textContent = 'Précédent';
                prevButton.addEventListener('click', () => fetchPlayers(currentPage - 1));
                pagination.appendChild(prevButton);
            }

            for (let i = currentPage - halfMaxPages; i <= currentPage + halfMaxPages; i++) {
                if (i >= 1 && i <= totalPages) {
                    const pageButton = document.createElement('button');
                    pageButton.textContent = i;
                    pageButton.addEventListener('click', () => fetchPlayers(i));

                    if (i === currentPage) {
                        pageButton.disabled = true;
                    }

                    pagination.appendChild(pageButton);
                }
            }

            if (currentPage < totalPages) {
                const nextButton = document.createElement('button');
                nextButton.textContent = 'Suivant';
                nextButton.addEventListener('click', () => fetchPlayers(currentPage + 1));
                pagination.appendChild(nextButton);
            }
        }

        function updateTeamPagination(currentPage, totalPages) {
            const pagination = document.getElementById('team-pagination');
            pagination.innerHTML = "";

            const maxPagesToShow = 5;
            const halfMaxPages = Math.floor(maxPagesToShow / 2);

            for (let i = currentPage - halfMaxPages; i <= currentPage + halfMaxPages; i++) {
                if (i >= 1 && i <= totalPages) {
                    const pageButton = document.createElement('button');
                    pageButton.textContent = i;
                    pageButton.addEventListener('click', () => fetchTeams(i));

                    if (i === currentPage) {
                        pageButton.disabled = true;
                    }

                    pagination.appendChild(pageButton);
                }
            }

        }

        function updateMatchPagination(currentPage, totalPages) {
            const pagination = document.getElementById('match-pagination');
            pagination.innerHTML = "";

            const maxPagesToShow = 5;
            const halfMaxPages = Math.floor(maxPagesToShow / 2);

            if (currentPage > 1) {
                const prevButton = document.createElement('button');
                prevButton.textContent = 'Précédent';
                prevButton.addEventListener('click', () => fetchMatches(currentPage - 1));
                pagination.appendChild(prevButton);
            }

            for (let i = currentPage - halfMaxPages; i <= currentPage + halfMaxPages; i++) {
                if (i >= 1 && i <= totalPages) {
                    const pageButton = document.createElement('button');
                    pageButton.textContent = i;
                    pageButton.addEventListener('click', () => fetchMatches(i));

                    if (i === currentPage) {
                        pageButton.disabled = true;
                    }

                    pagination.appendChild(pageButton);
                }
            }

            if (currentPage < totalPages) {
                const nextButton = document.createElement('button');
                nextButton.textContent = 'Suivant';
                nextButton.addEventListener('click', () => fetchMatches(currentPage + 1));
                pagination.appendChild(nextButton);
            }
        }

        function goToPlayerPage() {
            const pageNumber = parseInt(document.getElementById('player-page-number').value);
            if (pageNumber >= 1 && pageNumber <= 206) {
                currentPlayerPage = pageNumber;
                fetchPlayers(currentPlayerPage);
            }
        }

        function goToMatchPage() {
            const pageNumber = parseInt(document.getElementById('match-page-number').value);
            if (pageNumber >= 1) {
                currentMatchPage = pageNumber;
                fetchMatches(currentMatchPage);
            }
        }

        function searchPlayers() {
            fetchPlayers(1);
        }

        function resetPlayerSearch() {
            document.getElementById('player-search').value = "";
            fetchPlayers(1);
        }

        function searchTeams() {
            fetchTeams(1);
        }

        function resetTeamSearch() {
            document.getElementById('team-search').value = "";
            fetchTeams(1);
        }

        var modal = document.getElementById("myModal");
        var btn = document.getElementById("popup-button");
        var span = document.getElementsByClassName("close")[0];

        btn.onclick = function() {
            modal.style.display = "block";
            fetchPlayers(1);
        }

        span.onclick = function() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        var teamModal = document.getElementById("teamModal");
        var teamBtn = document.getElementById("team-popup-button");
        var teamSpan = document.getElementsByClassName("close")[1];

        teamBtn.onclick = function() {
            teamModal.style.display = "block";
            fetchTeams(1);
        }

        teamSpan.onclick = function() {
            teamModal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == teamModal) {
                teamModal.style.display = "none";
            }
        }

        var matchModal = document.getElementById("matchModal");
        var matchBtn = document.getElementById("match-popup-button");
        var matchSpan = document.getElementsByClassName("close")[2];

        matchBtn.onclick = function() {
            matchModal.style.display = "block";
            fetchMatches(1);
        }

        matchSpan.onclick = function() {
            matchModal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == matchModal) {
                matchModal.style.display = "none";
            }
        }

        fetchAllTeams();
    </script>
</body>
</html>
