<!DOCTYPE html>
<html lang="bn">
<head>
<meta charset="UTF-8">
<title>কোন সংখ্যা কাছাকাছি?</title>
<script src="https://cdnjs.cloudflare.com/ajax/libs/p5.js/1.4.2/p5.min.js"></script>
<style>
  body {
    margin:0;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
    background: linear-gradient(45deg, #ffcccb, #a8e6a3);
    font-family: 'Comic Sans MS', Arial, sans-serif;
  }
  canvas {
    border:4px solid #333;
    border-radius:15px;
    box-shadow:0 0 20px rgba(0,0,0,0.3);
  }
</style>
</head>
<body>
<script>
let score = 0;
let target;
let options = [];
let feedback = '';
let feedbackTimer = 0;

let colors = ['#ff6b6b', '#4ecdc4', '#45b7d1'];
let banglaNums = ['০','১','২','৩','৪','৫','৬','৭','৮','৯'];

function toBangla(num) {
  return num.toString().split('').map(d=>banglaNums[Number(d)]).join('');
}

function setup() {
  createCanvas(800,600);
  textAlign(CENTER,CENTER);
  textSize(48);
  newRound();
}

function newRound() {
  target = Math.floor(random(10, 100)); // target number

  // Generate 3 options including closest
  let closest = target - Math.floor(random(1,10)); // closest smaller
  let far1 = target + Math.floor(random(5,50));
  let far2 = target + Math.floor(random(50,100));

  options = shuffle([closest, far1, far2]);

  feedback = '';
  feedbackTimer = 0;
}

function draw() {
  background(255);

  // Question on top
  fill(0);
  textSize(36);
  text(`${toBangla(target)} এর কাছের সংখ্যাটি কত?`, width/2, 50);

  // Score on left
  textSize(28);
  textAlign(LEFT,CENTER);
  text('স্কোর: '+score, 20, 30);
  textAlign(CENTER,CENTER);

  // Show options
  textSize(60);
  for(let i=0;i<options.length;i++){
    fill(colors[i % colors.length]);
    rect(150 + i*200, 300, 150, 150, 20);
    fill(255);
    text(toBangla(options[i]), 225 + i*200, 375);
  }

  // Feedback
  if(feedback){
    fill(feedback.includes('অসাধারণ') ? 'green' : 'red');
    textSize(36);
    text(feedback, width/2, 500);
    feedbackTimer--;
    if(feedbackTimer<=0) feedback = '';
  }
}

function mousePressed(){
  for(let i=0;i<options.length;i++){
    if(mouseX > 150 + i*200 && mouseX < 300 + i*200 && mouseY > 300 && mouseY < 450){
      let closest = options.reduce((a,b)=> Math.abs(a-target)<Math.abs(b-target)?a:b);
      if(options[i] === closest){
        score++;
        feedback = 'অসাধারণ!'; // Great
      }else{
        score = max(0, score-1);
        feedback = 'আবার চেষ্টা করো!'; // Try Again
      }
      feedbackTimer = 60;
      setTimeout(newRound, 500);
    }
  }
}
</script>
</body>
</html>
