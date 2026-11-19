<?php
require 'includes/db.php';
require 'includes/functions.php';
require 'includes/header_root.php';
?>

<div class="row align-items-center mb-5">
    <div class="col-md-7">
        <h1 class="display-4 text-primary fw-bold mb-3">Welcome to Full Stack Jeopardy!</h1>
        <p class="lead">
            This project is a full-stack, responsive web application built using **PHP**, **MySQL**, **CSS**, and **HTML**. It features a complete user authentication system and a functional Jeopardy-style quiz game.
        </p>
        <p>
            Test your knowledge against categories like HISTORY, MUSIC, and MOVIES, earn points, and climb the global Leaderboard!
        </p>
        
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="game.php" class="btn btn-warning btn-lg mt-3 me-3 shadow-sm">
                Start Playing Now!
            </a>
            <a href="pages/profile.php" class="btn btn-outline-primary btn-lg mt-3">
                View Profile
            </a>
        <?php else: ?>
            <a href="pages/register.php" class="btn btn-success btn-lg mt-3 me-3 shadow-sm">
                Register to Play
            </a>
            <a href="pages/login.php" class="btn btn-outline-secondary btn-lg mt-3">
                Login
            </a>
        <?php endif; ?>
    </div>
    
    <div class="col-md-5 text-center mt-4 mt-md-0">
        <img src="img/logo.png" alt="Jeopardy Game Board" class="img-fluid rounded shadow-lg" style="max-height: 300px;">
        <p class="mt-2 text-muted small">Example game graphic </p>
    </div>
</div>

<hr>

<h2>💡 Project Features Overview</h2>

<div class="row">
    <div class="col-md-4 mb-3">
        <h4>User Authentication</h4>
        <p>Secure registration, login, and profile management using <code>password_hash()</code> and PDO for database security.</p>
    </div>
    <div class="col-md-4 mb-3">
        <h4>Dynamic Gameplay</h4>
        <p>Game board loads dynamically from MySQL, updates scores in real-time using JavaScript/AJAX, and tracks user statistics.</p>
    </div>
    <div class="col-md-4 mb-3">
        <h4>Responsive Design</h4>
        <p>Built with Bootstrap 5, the entire site is mobile-responsive and features a consistent theme and design across all pages.</p>
    </div>
</div>


<?php 
// Since index.php is in the root, we include includes/footer.php directly
require 'includes/footer.php'; 
?>