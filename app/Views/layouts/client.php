<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle ?? 'Flow One'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($appBaseLinkPath ?? ''); ?>/css/style.css">
</head>
<body>
    <div class="d-flex flex-column vh-100"> <!-- app-container replacement -->
        <!-- Top Navbar (already implemented in previous step) -->
        <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
            <div class="container-fluid">
                <a class="navbar-brand" href="<?php echo htmlspecialchars($appBaseLinkPath ?? ''); ?>/dashboard">
                    <!-- You can add a logo here if you have one, or just text -->
                    Flow One Client
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNavDropdown">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarUserDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <!-- Placeholder for round icon - using Bootstrap Icons -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                                    <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                                    <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
                                </svg>
                                <span class="ms-1"><?php echo htmlspecialchars(Session::get('user_name', 'User')); ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarUserDropdown">
                                <li><a class="dropdown-item" href="<?php echo htmlspecialchars($appBaseLinkPath ?? ''); ?>/client/settings">Settings</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?php echo htmlspecialchars($appBaseLinkPath ?? ''); ?>/logout">Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="d-flex flex-grow-1 overflow-hidden"> <!-- Container for sidebar and main content -->
            <nav class="app-sidebar bg-light border-end p-3" style="width: 280px;"> <!-- Sidebar -->
                <h5 class="mb-3">Client Menu</h5>
                <ul class="nav nav-pills flex-column mb-auto">
                    <li class="nav-item">
                        <a href="<?php echo htmlspecialchars($appBaseLinkPath ?? ''); ?>/dashboard" class="nav-link link-dark"> <!-- Points to main dashboard route -->
                            <svg class="bi me-2" width="16" height="16"><use xlink:href="#home"/></svg> <!-- Placeholder for icon -->
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo htmlspecialchars($appBaseLinkPath ?? ''); ?>/client/trainees" class="nav-link link-dark">
                            <svg class="bi me-2" width="16" height="16"><use xlink:href="#people-circle"/></svg>
                            My trainees
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo htmlspecialchars($appBaseLinkPath ?? ''); ?>/client/trainings" class="nav-link link-dark">
                            <svg class="bi me-2" width="16" height="16"><use xlink:href="#collection"/></svg>
                            Trainings
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo htmlspecialchars($appBaseLinkPath ?? ''); ?>/client/exercises" class="nav-link link-dark">
                            <svg class="bi me-2" width="16" height="16"><use xlink:href="#clipboard-check"/></svg>
                            Exercises
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo htmlspecialchars($appBaseLinkPath ?? ''); ?>/client/forum" class="nav-link link-dark">
                            <svg class="bi me-2" width="16" height="16"><use xlink:href="#chat-dots"/></svg>
                            Forum
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo htmlspecialchars($appBaseLinkPath ?? ''); ?>/client/playlist" class="nav-link link-dark">
                            <svg class="bi me-2" width="16" height="16"><use xlink:href="#music-note-list"/></svg>
                            Play List
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo htmlspecialchars($appBaseLinkPath ?? ''); ?>/client/shop" class="nav-link link-dark">
                            <svg class="bi me-2" width="16" height="16"><use xlink:href="#cart"/></svg>
                            Shop
                        </a>
                    </li>
                </ul>
                <!-- SVG definitions for icons (can be placed at the bottom of body or in a separate file) -->
                <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
                    <symbol id="home" viewBox="0 0 16 16">
                        <path d="M8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4.5a.5.5 0 0 0 .5-.5v-4h2v4a.5.5 0 0 0 .5.5H14a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146zM2.5 14V7.707l5.5-5.5 5.5 5.5V14H10v-4a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5v4H2.5z"/>
                    </symbol>
                    <symbol id="people-circle" viewBox="0 0 16 16">
                        <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                        <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
                    </symbol>
                    <symbol id="collection" viewBox="0 0 16 16">
                        <path d="M2.5 3.5a.5.5 0 0 1 0-1h11a.5.5 0 0 1 0 1h-11zm2-2a.5.5 0 0 1 0-1h7a.5.5 0 0 1 0 1h-7zM0 13a1.5 1.5 0 0 0 1.5 1.5h13A1.5 1.5 0 0 0 16 13V6a1.5 1.5 0 0 0-1.5-1.5h-13A1.5 1.5 0 0 0 0 6v7zm1.5.5A.5.5 0 0 1 1 13V6a.5.5 0 0 1 .5-.5h13a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-.5.5h-13z"/>
                    </symbol>
                    <symbol id="clipboard-check" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M10.854 7.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 9.793l2.646-2.647a.5.5 0 0 1 .708 0z"/>
                        <path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1v-1z"/>
                        <path d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5h3zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3z"/>
                    </symbol>
                    <symbol id="chat-dots" viewBox="0 0 16 16">
                        <path d="M5 8a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm4 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 1a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
                        <path d="M2.165 15.803l.02-.004c1.83-.363 2.948-.842 3.468-1.105A9.06 9.06 0 0 0 8 15c4.418 0 8-3.134 8-7s-3.582-7-8-7-8 3.134-8 7c0 1.76.743 3.37 1.97 4.6a10.437 10.437 0 0 1-.524 2.318l-.003.011a10.722 10.722 0 0 1-.244.637c-.079.186.074.394.273.362a21.673 21.673 0 0 0 .693-.125zm.8-3.108a1 1 0 0 0-.287-.801C1.618 10.83 1 9.468 1 8c0-3.192 3.004-6 7-6s7 2.808 7 6c0 3.193-3.004 6-7 6a8.06 8.06 0 0 1-2.088-.272 1 1 0 0 0-.711.074c-.387.196-1.24.57-2.634.893a10.97 10.97 0 0 0 .398-2z"/>
                    </symbol>
                    <symbol id="music-note-list" viewBox="0 0 16 16">
                        <path d="M12 13c0 1.105-1.12 2-2.5 2S7 14.105 7 13s1.12-2 2.5-2 2.5.895 2.5 2z"/>
                        <path fill-rule="evenodd" d="M12 3v10h-1V3h1z"/>
                        <path d="M11 2.82a1 1 0 0 1 .804-.98l3-.6A1 1 0 0 1 16 2.22V4l-5 1V2.82z"/>
                        <path fill-rule="evenodd" d="M0 11.5a.5.5 0 0 1 .5-.5H4a.5.5 0 0 1 0 1H.5a.5.5 0 0 1-.5-.5zm0-4A.5.5 0 0 1 .5 7H8a.5.5 0 0 1 0 1H.5a.5.5 0 0 1-.5-.5zm0-4A.5.5 0 0 1 .5 3H8a.5.5 0 0 1 0 1H.5a.5.5 0 0 1-.5-.5z"/>
                    </symbol>
                    <symbol id="cart" viewBox="0 0 16 16">
                        <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l1.313 7h8.17l1.313-7H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                    </symbol>
                </svg>
            </nav>

            <main class="app-content p-4 flex-grow-1 overflow-y-auto"> <!-- Main content area -->
                <?php echo $content ?? '<h1>Main Content Area Placeholder</h1><p>Error: Content not loaded.</p>'; ?>
            </main>
        </div>

        <footer class="app-footer bg-light border-top p-3 text-center"> <!-- Footer -->
            <p class="mb-0">&copy; <?php echo date('Y'); ?> Flow One. All rights reserved.</p>
        </footer>
    </div>
    <script src="<?php echo htmlspecialchars($appBaseLinkPath ?? ''); ?>/js/jquery.min.js"></script>
    <script src="<?php echo htmlspecialchars($appBaseLinkPath ?? ''); ?>/js/app.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>
