let upPressed = false;
let downPressed = false;
let leftPressed = false;
let rightPressed = false;

const main = document.querySelector('main');

//Player = 2, Wall = 1, Enemy = 3, Point = 0
let maze = [
    [1, 1, 1, 1, 1, 1, 1, 1, 1, 1],
    [1, 2, 0, 1, 0, 0, 0, 0, 3, 1],
    [1, 0, 0, 0, 0, 0, 0, 1, 1, 1],
    [1, 0, 0, 0, 0, 0, 0, 0, 0, 1],
    [1, 0, 1, 1, 0, 0, 0, 0, 0, 1],
    [1, 0, 0, 0, 0, 0, 0, 1, 1, 1],
    [1, 0, 0, 1, 0, 3, 0, 0, 0, 1],
    [1, 0, 0, 0, 0, 0, 0, 1, 0, 1],
    [1, 3, 1, 0, 0, 0, 0, 0, 0, 1],
    [1, 1, 1, 1, 1, 1, 1, 1, 1, 1]
];

//Populates the maze in the HTML
for (let y of maze) {
    for (let x of y) {
        let block = document.createElement('div');
        block.classList.add('block');

        switch (x) {
            case 1:
                block.classList.add('wall');
                break;
            case 2:
                block.id = 'player';
                let mouth = document.createElement('div');
                mouth.classList.add('mouth');
                block.appendChild(mouth);
                break;
            case 3:
                block.classList.add('enemy');
                break;
            default:
                block.classList.add('point');
                block.style.height = '1vh';
                block.style.width = '1vh';
        }

        main.appendChild(block);
    }
}

//Player movement
function keyUp(event) {
    if (event.key === 'ArrowUp') {
        upPressed = false;
    } else if (event.key === 'ArrowDown') {
        downPressed = false;
    } else if (event.key === 'ArrowLeft') {
        leftPressed = false;
    } else if (event.key === 'ArrowRight') {
        rightPressed = false;
    }
}

function keyDown(event) {
    if (event.key === 'ArrowUp') {
        upPressed = true;
    } else if (event.key === 'ArrowDown') {
        downPressed = true;
    } else if (event.key === 'ArrowLeft') {
        leftPressed = true;
    } else if (event.key === 'ArrowRight') {
        rightPressed = true;
    }
}

const player = document.querySelector('#player');
const playerMouth = player.querySelector('.mouth');
let playerTop = 0;
let playerLeft = 0;

setInterval(function() {
    if(downPressed) {
        playerTop++;
        player.style.top = playerTop + 'px';
        playerMouth.classList = 'down';
    }
    else if(upPressed) {
        playerTop--;
        player.style.top = playerTop + 'px';
        playerMouth.classList = 'up';
    }
    else if(leftPressed) {
        playerLeft--;
        player.style.left = playerLeft + 'px';
        playerMouth.classList = 'left';
    }
    else if(rightPressed) {
        playerLeft++;
        player.style.left = playerLeft + 'px';
        playerMouth.classList = 'right';
    }
}, 10);

document.addEventListener('keydown', keyDown);
document.addEventListener('keyup', keyUp);

/* ===============================
   GAME STATE & START CONTROL
================================ */

let gameStarted = false;
let gameOver = false;
let score = 0;

const startDiv = document.querySelector('.startDiv');
const scoreDisplay = document.querySelector('.score p');
const enemies = document.querySelectorAll('.enemy');
const blocks = document.querySelectorAll('.block');

startDiv.addEventListener('click', () => {
    startDiv.style.display = 'none';
    gameStarted = true;
});

/* ===============================
   PREVENT MOVEMENT BEFORE START
================================ */

const originalKeyDown = keyDown;
function keyDown(event) {
    if (!gameStarted || gameOver) return;
    originalKeyDown(event);
}

/* ===============================
   GRID CALCULATIONS
================================ */

const gridSize = 80; // 80vh / 10 = each cell
function getPlayerGridPosition() {
    return {
        x: Math.round(player.offsetLeft / gridSize),
        y: Math.round(player.offsetTop / gridSize)
    };
}

/* ===============================
   WALL COLLISION
================================ */

function isWall(x, y) {
    return maze[y][x] === 1;
}

/* ===============================
   PLAYER MOVEMENT FIX
================================ */

setInterval(() => {
    if (!gameStarted || gameOver) return;

    let pos = getPlayerGridPosition();
    let newX = pos.x;
    let newY = pos.y;

    if (upPressed) newY--;
    if (downPressed) newY++;
    if (leftPressed) newX--;
    if (rightPressed) newX++;

    if (!isWall(newX, newY)) {
        player.style.top = newY * gridSize + 'px';
        player.style.left = newX * gridSize + 'px';
    }

    collectPoint(newX, newY);
    checkEnemyCollision();
}, 120);

/* ===============================
   POINT COLLECTION
================================ */

function collectPoint(x, y) {
    const index = y * 10 + x;
    const block = blocks[index];

    if (block && block.classList.contains('point')) {
        block.classList.remove('point');
        score += 10;
        scoreDisplay.textContent = score;

        checkWin();
    }
}

/* ===============================
   WIN CONDITION
================================ */

function checkWin() {
    if (document.querySelectorAll('.point').length === 0) {
        endGame("YOU WIN! Restart?");
    }
}

/* ===============================
   ENEMY MOVEMENT
================================ */

enemies.forEach(enemy => {
    let dir = Math.floor(Math.random() * 4);

    setInterval(() => {
        if (!gameStarted || gameOver) return;

        let x = Math.round(enemy.offsetLeft / gridSize);
        let y = Math.round(enemy.offsetTop / gridSize);

        let nx = x;
        let ny = y;

        if (dir === 0) ny--;
        if (dir === 1) ny++;
        if (dir === 2) nx--;
        if (dir === 3) nx++;

        if (maze[ny][nx] === 1) {
            dir = Math.floor(Math.random() * 4);
        } else {
            enemy.style.top = ny * gridSize + 'px';
            enemy.style.left = nx * gridSize + 'px';
        }
    }, 400);
});

/* ===============================
   ENEMY COLLISION
================================ */

function checkEnemyCollision() {
    enemies.forEach(enemy => {
        if (
            Math.abs(enemy.offsetLeft - player.offsetLeft) < gridSize / 2 &&
            Math.abs(enemy.offsetTop - player.offsetTop) < gridSize / 2
        ) {
            player.classList.add('dead');
            endGame("GAME OVER! Restart?");
        }
    });
}

/* ===============================
   GAME OVER + RESTART
================================ */

function endGame(message) {
    gameOver = true;

    startDiv.style.display = 'flex';
    startDiv.querySelector('h1').textContent = message;

    startDiv.onclick = () => location.reload();
}