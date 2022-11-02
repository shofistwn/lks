// game catch the ball using javascript

// create a canvas
var canvas = document.getElementById("canvas");
var ctx = canvas.getContext("2d");

// set the width and height of the canvas
canvas.width = 800;
canvas.height = 600;

// create the ball object
var ball = {
    x: 400,
    y: 300,
    radius: 20,
    color: "red",
    speed: 5,
    dx: 5,
    dy: -4
};

// create the user paddle
var user = {
    x: 0,
    y: (canvas.height - 100) / 2,
    width: 10,
    height: 100,
    color: "blue",
    score: 0
};

// create the com paddle
var com = {
    x: canvas.width - 10,
    y: (canvas.height - 100) / 2,
    width: 10,
    height: 100,
    color: "blue",
    score: 0
};

// create the net
var net = {
    x: (canvas.width - 2) / 2,
    y: 0,
    width: 2,
    height: 10,
    color: "blue"
};

// draw the net
function drawNet() {
    for (var i = 0; i <= canvas.height; i += 15) {
        drawRect(net.x, net.y + i, net.width, net.height, net.color);
    }
}

// draw the rectangle
function drawRect(x, y, w, h, color) {
    ctx.fillStyle = color;
    ctx.fillRect(x, y, w, h);
}

// draw the circle
function drawCircle(x, y, r, color) {
    ctx.fillStyle = color;
    ctx.beginPath();
    ctx.arc(x, y, r, 0, Math.PI * 2, true);
    ctx.closePath();
    ctx.fill();
}

// draw the text
function drawText(text, x, y, color) {
    ctx.fillStyle = color;
    ctx.font = "45px fantasy";
    ctx.fillText(text, x, y);
}

// render the game
function render() {
    // clear the canvas
    drawRect(0, 0, canvas.width, canvas.height, "black");

    // draw the net
    drawNet();

    // draw the score
    drawText(user.score, canvas.width / 4, canvas.height / 5, "white");
    drawText(com.score, 3 * canvas.width / 4, canvas.height / 5, "white");

    // draw the user and com paddle
    drawRect(user.x, user.y, user.width, user.height, user.color);
    drawRect(com.x, com.y, com.width, com.height, com.color);

    // draw the ball
    drawCircle(ball.x, ball.y, ball.radius, ball.color);
}

// control the user paddle
canvas.addEventListener("mousemove", movePaddle);

function movePaddle(evt) {
    var rect = canvas.getBoundingClientRect();

    user.y = evt.clientY - rect.top - user.height / 2;
}

// collision detection
function collision(b, p) {
    p.top = p.y;
    p.bottom = p.y + p.height;
    p.left = p.x;
    p.right = p.x + p.width;

    b.top = b.y - b.radius;
    b.bottom = b.y + b.radius;
    b.left = b.x - b.radius;
    b.right = b.x + b.radius;

    return b.right > p.left && b.bottom > p.top && b.left < p.right && b.top < p.bottom;
}

// reset the ball
function resetBall() {
    ball.x = canvas.width / 2;
    ball.y = canvas.height / 2;
    ball.speed = 5;
    ball.dx = -ball.dx;
}

// update the ball position
function update() {
    ball.x += ball.dx;
    ball.y += ball.dy;

    // simple AI to control the com paddle
    var computerLevel = 0.1;
    com.y += (ball.y - (com.y + com.height / 2)) * computerLevel;

    if (ball.y + ball.radius > canvas.height || ball.y - ball.radius < 0) {
        ball.dy = -ball.dy;
    }

    var player = (ball.x < canvas.width / 2) ? user : com;

    if (collision(ball, player)) {
        // where the ball hit the player
        var collidePoint = (ball.y - (player.y + player.height / 2));

        // normalize the value of collidePoint, we need to get numbers between -1 and 1.
        // -player.height/2 < collidePoint < player.height/2
        collidePoint = collidePoint / (player.height / 2);

        // calculate the angle of the ball
        var angleRad = (Math.PI / 4) * collidePoint;

        // direction of the ball when it bounces off a paddle
        var direction = (ball.x < canvas.width / 2) ? 1 : -1;

        // change the X and Y velocity direction
        ball.dx = direction * ball.speed * Math.cos(angleRad);
        ball.dy = ball.speed * Math.sin(angleRad);

        // speed up the ball everytime a paddle hits it.
        ball.speed += 0.1;
    }

    // update the score
    if (ball.x - ball.radius < 0) {
        // the com win
        com.score++;
        resetBall();
    } else if (ball.x + ball.radius > canvas.width) {
        // the user win
        user.score++;
        resetBall();
    }
}

// game init
function game() {
    update();
    render();
}

// loop
var framePerSecond = 50;
setInterval(game, 1000 / framePerSecond);