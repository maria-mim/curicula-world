<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Bangla Number Learning Game</title>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/p5.js/1.4.2/p5.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/p5.js/1.4.2/addons/p5.sound.min.js"></script>
  <style>
    body {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
      background: linear-gradient(45deg, #ffcccb, #add8e6);
      font-family: 'Comic Sans MS', Arial, sans-serif;
    }
    canvas {
      border: 4px solid #333;
      border-radius: 15px;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
    }
    #instructions {
      position: absolute;
      top: 20px;
      text-align: center;
      font-size: 28px;
      color: #fff;
      text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
    }
  </style>
</head>
<body>
  <div id="instructions"></div>
  <script>
    let numbers = [
      { digit: '০', text: 'শূন্য', sound: null },
      { digit: '১', text: 'এক', sound: null },
      { digit: '২', text: 'দুই', sound: null },
      { digit: '৩', text: 'তিন', sound: null },
      { digit: '৪', text: 'চার', sound: null },
      { digit: '৫', text: 'পাঁচ', sound: null },
      { digit: '৬', text: 'ছয়', sound: null },
      { digit: '৭', text: 'সাত', sound: null },
      { digit: '৮', text: 'আট', sound: null },
      { digit: '৯', text: 'নয়', sound: null }
    ];

    let currentNumber;
    let options = [];
    let score = 0;
    let soundLoaded = false;
    let feedback = '';
    let feedbackTimer = 0;
    let bounceOffset = 0;
    let shakeOffset = 0;
    let colors = ['#ff6b6b', '#4ecdc4', '#45b7d1', '#96ceb4', '#ffeead', '#d4a5a5', '#9b59b6', '#3498db', '#e74c3c', '#2ecc71'];

    function preload() {
      soundLoaded = true; // no sounds for now
    }

    function setup() {
      createCanvas(800, 600);
      textAlign(CENTER, CENTER);
      textSize(48);
      selectNewNumber();
    }

    function selectNewNumber() {
      currentNumber = random(numbers);
      options = [currentNumber];
      while (options.length < 3) {
        let opt = random(numbers);
        if (!options.includes(opt)) options.push(opt);
      }
      options = shuffle(options);
      feedback = '';
      bounceOffset = 0;
      shakeOffset = 0;
    }

    function draw() {
      background(255);
      bounceOffset = sin(frameCount * 0.1) * 10;

      fill(255, 165, 0);
      textSize(120);
      text(currentNumber.digit, width / 2 + shakeOffset, 100 + bounceOffset);

      fill(255, 255, 255, 200);
      rect(width / 2 - 100, 150, 200, 60, 20);

      fill(0);
      textSize(48);
      text(currentNumber.text, width / 2, 180);
      text(`Score: ${score}`, width / 2, 50); // fixed template literal

      for (let i = 0; i < options.length; i++) {
        fill(colors[i % colors.length]);
        rect(150 + i * 200, 300, 150, 150, 20);
        fill(255);
        textSize(60);
        text(options[i].digit, 225 + i * 200, 375);
      }

      if (feedback) {
        fill(feedback.includes('Great') ? 'green' : 'red');
        textSize(36);
        text(feedback, width / 2, 500);
        feedbackTimer--;
        if (feedbackTimer <= 0) feedback = '';
      }

      if (feedback.includes('Great')) {
        for (let i = 0; i < 5; i++) {
          fill(random(255), random(255), 255, 200);
          ellipse(random(width / 2 - 50, width / 2 + 50), random(50, 150), 10, 10);
        }
      }
    }

    function mousePressed() {
      for (let i = 0; i < options.length; i++) {
        if (mouseX > 150 + i * 200 && mouseX < 300 + i * 200 && mouseY > 300 && mouseY < 450) {
          if (options[i] === currentNumber) {
            score++;
            feedback = 'Great Job!';
            feedbackTimer = 60;
            selectNewNumber();
          } else {
            score = max(0, score - 1);
            feedback = 'Try Again!';
            feedbackTimer = 60;
            shakeOffset = 10;
            setTimeout(() => shakeOffset = 0, 200);
          }
        }
      }
    }
  </script>
</body>
</html>
