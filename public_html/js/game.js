// js/game_logic.js (or game.js, ensure the name matches your <script> tag)

let currentScore = 0;
let currentQID = 0;
let currentValue = 0; // Track the point value for scoring

// 1. **FIXED SELECTOR:** Use '.jeopardy-tile' instead of '.question-block'
document.querySelectorAll('.jeopardy-tile').forEach(block => {
    block.addEventListener('click', function() {
        // 2. **FIXED ATTRIBUTE:** Use 'data-question-id' instead of 'data-id'
        let id = this.getAttribute('data-question-id');
        let value = this.getAttribute('data-value'); // Get the point value

        // Check if the tile is already clicked (we use the 'disabled' class name)
        if (!id || this.classList.contains('disabled')) return;
        
        currentQID = id;
        currentValue = parseInt(value); // Store the point value
        
        this.classList.add('disabled'); // Disable clicked question
        this.classList.remove('bg-info'); 
        this.classList.add('bg-secondary'); // Visual cue (Grey out)

        // Fetch Question
        fetch('api_game_logic.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=get_question&id=${id}`
        })
        .then(res => res.json())
        .then(data => {
            // Check for error/null data before accessing properties
            if (data.error) {
                document.getElementById('qText').innerText = "Error fetching question.";
            } else {
                document.getElementById('qText').innerText = data.question_text;
            }
            
            // Set the value into the modal header (assuming you have one)
            document.getElementById('questionValue').innerText = `$${value}`; 
            
            document.getElementById('userAnswer').value = '';
            document.getElementById('feedback').innerText = '';
            // Assuming you have a questionModal element defined in game.php
            new bootstrap.Modal(document.getElementById('questionModal')).show();
        });
    });
});

function submitAnswer() {
    let ans = document.getElementById('userAnswer').value;
    
    fetch('api_game_logic.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=check_answer&id=${currentQID}&answer=${ans}`
    })
    .then(res => res.json())
    .then(data => {
        let feedback = document.getElementById('feedback');
        
        // Use the value stored earlier (currentValue) if data.value isn't passed back
        const points = data.value ? parseInt(data.value) : currentValue; 
        
        if (data.correct) {
            currentScore += points;
            feedback.innerHTML = '<span class="text-success">Correct!</span>';
        } else {
            currentScore -= points;
            // The API response MUST contain correct_answer for this line to work.
            feedback.innerHTML = `<span class="text-danger">Wrong! Answer was: ${data.correct_answer}</span>`;
        }
        // 3. **FIXED SELECTOR:** Use the correct selector for the score display
        document.querySelector('.text-secondary').innerText = `Score: $${currentScore}`;
        
        setTimeout(() => {
            // Dismiss the modal
            bootstrap.Modal.getInstance(document.getElementById('questionModal')).hide();
        }, 2000);
    });
}

document.getElementById('endGameBtn').addEventListener('click', function() {
    fetch('api_game_logic.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `action=save_game&score=${currentScore}`
    })
    .then(() => {
        alert('Game Over! Score saved.');
        // 4. **FIXED PATH:** Redirect to the profile page in the current directory structure
        window.location.href = 'profile.php'; 
    });
});