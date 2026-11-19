<?php
// pages/project.php
session_start();
require '../includes/db.php'; 
require '../includes/header_pages.php'; // Assuming header is in includes/

// Define your projects here
$projects = [
    [
        'title' => 'The Jeopardy Game',
        'description' => 'A fully functional, dynamic trivia game built with PHP, MySQL (PDO), and AJAX (JavaScript). Features user authentication, score tracking, and a leaderboard.',
        'link_text' => 'Play the Game',
        'link_url' => '../game.php', // Link to your game page
        'tags' => ['PHP', 'MySQL', 'AJAX', 'Bootstrap']
    ],
    [
        'title' => 'User Account System',
        'description' => 'A secure user management system including registration, login, profile pages, and password security using password_hash().',
        'link_text' => 'View Profile',
        'link_url' => 'profile.php', // Link to your profile page
        'tags' => ['Security', 'Authentication', 'Session Management']
    ],
    [
        'title' => 'Website Foundation',
        'description' => 'The overall site structure, responsive layout, consistent styling, and navigation across all pages (Landing, Contact, Project).',
        'link_text' => 'Go Home',
        'link_url' => '../index.php', // Link to your landing page
        'tags' => ['HTML5', 'CSS3', 'Bootstrap', 'Responsiveness']
    ],
    // You can add more projects here if required by the rubric
];
?>

<div class="row">
    <div class="col-lg-10 offset-lg-1">
        <h2 class="mb-4 text-primary">💼 My Portfolio Projects</h2>
        <p class="lead">Here is a list of the key components and projects developed for this site.</p>
        
        <div class="row mt-5">
            <?php foreach ($projects as $project): ?>
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm h-100 border-primary">
                        <div class="card-body">
                            <h3 class="card-title text-info"><?= htmlspecialchars($project['title']) ?></h3>
                            <p class="card-text"><?= htmlspecialchars($project['description']) ?></p>
                            
                            <div class="mb-3">
                                <?php foreach ($project['tags'] as $tag): ?>
                                    <span class="badge bg-secondary me-1"><?= $tag ?></span>
                                <?php endforeach; ?>
                            </div>
                            
                            <a href="<?= htmlspecialchars($project['link_url']) ?>" class="btn btn-primary mt-2">
                                <?= htmlspecialchars($project['link_text']) ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    </div>
</div>

<?php require '../includes/footer.php'; ?>