<!DOCTYPE html>
<html lang="bn">
<head>
<meta charset="UTF-8">
<title>সহজে আকার শিখি</title>
<script src="https://cdnjs.cloudflare.com/ajax/libs/p5.js/1.4.2/p5.min.js"></script>
<style>
  body {
    margin: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background: linear-gradient(135deg, #a8e6a3, #2e7d32);
    font-family: 'SolaimanLipi', Arial, sans-serif;
  }
  canvas {
    border-radius: 15px;
    box-shadow: 0 0 25px rgba(0,0,0,0.3);
  }
</style>
</head>
<body>
<script>
let shapes = [
  {type: 'rect', name: 'বর্গ', color: '#ff6b6b'},
  {type: 'rectLong', name: 'আয়ত', color: '#4ecdc4'},
  {type: 'ellipse', name: 'বৃত্ত', color: '#45b7d1'},
  {type: 'triangle', name: 'ত্রিভুজ', color: '#ffeead'}
];

let currentShape = null; // ensure initialized
let options = [];
let score = 0;
let feedback = '';
let feedbackTimer = 0;

function setup() {
  createCanvas(800, 600);
  textAlign(CENTER, CENTER);
  textSize(32);
  selectNewShape(); // initialize currentShape before draw
}

function selectNewShape() {
  currentShape = random(shapes);
  options = [currentShape];
  while (options.length < 3) {
    let opt = random(shapes);
    if (!options.includes(opt)) options.push(opt);
  }
  options = shuffle(options);
  feedback = '';
}

function draw() {
  background('#eaf6ea');

  if (!currentShape) return; // safety check

  // Draw shape
  fill(currentShape.color);
  noStroke();
  switch(currentShape.type) {
    case 'rect':
      rectMode(CENTER);
      rect(width/2, 200, 150, 150);
      break;
    case 'rectLong':
      rectMode(CENTER);
      rect(width/2, 200, 200, 120);
      break;
    case 'ellipse':
      ellipse(width/2, 200, 150, 150);
      break;
    case 'triangle':
      triangle(width/2, 120, width/2-75, 280, width/2+75, 280);
      break;
  }

  // Draw option buttons
  textSize(32);
  for (let i = 0; i < options.length; i++) {
    let txt = options[i].name;
    let txtWidthVal = textWidth(txt);
    let boxWidth = max(txtWidthVal + 40, 120); // minimum width
    let boxX = width/2 + (i-1)*220;
    let boxY = 400;
    fill('#2e7d32');
    rect(boxX, boxY, boxWidth, 80, 15);
    fill('#fff');
    text(txt, boxX, boxY);

    // Save box info for click detection
    options[i].boxX = boxX;
    options[i].boxY = boxY;
    options[i].boxWidth = boxWidth;
    options[i].boxHeight = 80;
  }

  // Feedback
  if (feedback) {
    fill(feedback.includes('সঠিক') ? '#388e3c' : '#d32f2f');
    textSize(36);
    text(feedback, width/2, 520);
    feedbackTimer--;
    if (feedbackTimer <= 0) feedback = '';
  }

  // Score display
  fill('#2e7d32');
  textSize(28);
  text(`স্কোর: ${score}`, 100, 50); // left side
}

function mousePressed() {
  for (let i = 0; i < options.length; i++) {
    let o = options[i];
    if (mouseX > o.boxX - o.boxWidth/2 && mouseX < o.boxX + o.boxWidth/2 &&
        mouseY > o.boxY - o.boxHeight/2 && mouseY < o.boxY + o.boxHeight/2) {
      if (o === currentShape) {
        score++;
        feedback = 'সঠিক! 🎉';
      } else {
        feedback = 'ভুল! আবার চেষ্টা করো।';
        score = max(0, score-1);
      }
      feedbackTimer = 60;
      selectNewShape();
    }
  }
}
</script>
</body>
</html>
