<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complex Button</title>
    <style>
        @keyframes loading {
            0%   { cy: 10; }
            25%  { cy: 3; }
            50%  { cy: 10; }
        }

        body {
            -webkit-font-smoothing: antialiased;
            background-color: #f4f7ff;
        }

        canvas {
            height: 100vh;
            pointer-events: none;
            position: fixed;
            width: 100%;
            z-index: 2;
        }

        button {
            background: none;
            border: none;
            color: #f4f7ff;
            cursor: pointer;
            font-family: 'Quicksand', sans-serif;
            font-size: 14px;
            font-weight: 500;
            height: 40px;
            left: 50%;
            outline: none;
            overflow: hidden;
            padding: 0 10px;
            position: fixed;
            top: 50%;
            transform: translate(-50%, -50%);
            width: 190px;
            -webkit-tap-highlight-color: transparent;
            z-index: 1;
        }

        // State styles
        button::before {
            background: #1f2335;
            border-radius: 50px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, .4) inset;
            content: '';
            display: block;
            height: 100%;
            margin: 0 auto;
            position: relative;
            transition: width .2s cubic-bezier(.39,1.86,.64,1) .3s;
            width: 100%;
        }

        // READY STATE
        button.ready {
            .submitMessage svg {
                opacity: 1;
                top: 1px;
                transition: top .4s ease 600ms, opacity .3s linear 600ms;
            }

            .submitMessage .button-text {
                top: 0;
                opacity: 1;
                transition: all .2s ease calc(var(--dr) + 600ms);
            }
        }

        // LOADING STATE
        button.loading::before {
            transition: width .3s ease;
            width: 80%;
        }

        .loadingMessage {
            opacity: 1;
        }

        .loadingCircle {
            animation-duration: 1s;
            animation-iteration-count: infinite;
            animation-name: loading;
            cy: 10;
        }

        // COMPLETE STATE
        button.complete .submitMessage svg {
            top: -30px;
            transition: none;
        }

        .submitMessage .button-text {
            top: -8px;
            transition: none;
        }

        .loadingMessage {
            top: 80px;
        }

        .successMessage .button-text {
            left: 0;
            opacity: 1;
            transition: all .2s ease calc(var(--d) + 1000ms);
        }

        .successMessage svg { 
            stroke-dashoffset: 0;
            transition: stroke-dashoffset .3s ease-in-out 1.4s;
        }

        .loadingCircle:nth-child(2) { animation-delay: .1s }
        .loadingCircle:nth-child(3) { animation-delay: .2s }

        // Website Link
        .website-link {
            background: #f8faff;
            border-radius: 50px 0 0 50px;
            bottom: 30px;
            color: #324b77;
            cursor: pointer;
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            height: 34px;
            filter: drop-shadow(2px 3px 4px rgba(#000, .1));
            padding: 0 20px 0 40px;
            position: fixed;
            right: 0;
            text-align: left;
            text-decoration: none;
        }

        .website-link__icon {
            left: -10px;
            position: absolute;
            top: -12px;
            width: 44px;
        }

        .website-link__name {
            display: block;
            font-size: 14px;
            line-height: 14px;
            margin: 5px 0 3px;
        }

        .website-link__last-name {
            color: #55bada;
        }

        .website-link__message {
            color: #8aa8c5;
            display: block;
            font-size: 7px;
            line-height: 7px;
        }
    </style>
</head>
<body>
    <div>
        <button id="button" class="animated-btn">
            <div class="message submitMessage">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 13 12.2">
                    <polyline stroke="currentColor" points="2,7.1 6.5,11.1 11,7.1 "/>
                    <line stroke="currentColor" x1="6.5" y1="1.2" x2="6.5" y2="10.3"/>
                </svg>
                <span class="button-text">Realizar Pedido</span>
            </div>

            <div class="message loadingMessage">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 19 17">
                    <circle class="loadingCircle" cx="2.2" cy="10" r="1.6"/>
                    <circle class="loadingCircle" cx="9.5" cy="10" r="1.6"/>
                    <circle class="loadingCircle" cx="16.8" cy="10" r="1.6"/>
                </svg>
            </div>

            <div class="message successMessage">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 13 11">
                    <polyline stroke="currentColor" points="1.4,5.8 5.1,9.5 11.6,2.1 "/>
                </svg>
                <span class="button-text">Success</span>
            </div>
        </button>

        <canvas id="canvas"></canvas>
    </div>

    <script>
        // "physics" variables
        const confettiCount = 20;
        const sequinCount = 10;
        const gravityConfetti = 0.3;
        const gravitySequins = 0.55;
        const dragConfetti = 0.075;
        const dragSequins = 0.02;
        const terminalVelocity = 3;

        // init other global elements
        const button = document.getElementById('button');
        var disabled = false;
        const canvas = document.getElementById('canvas');
        const context = canvas.getContext('2d');
        let confettiArray = [];
        let sequinsArray = [];
        const colors = ['#ff6c6c', '#ff6c6c', '#7d63d5', '#e3e3e3', '#50e3c2'];

        // setup canvas
        function setCanvasSize() {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        }

        // create confetti and sequins
        function createParticles() {
            confettiArray = [];
            sequinsArray = [];
            for (let i = 0; i < confettiCount; i++) {
                confettiArray.push(new Confetti());
            }
            for (let i = 0; i < sequinCount; i++) {
                sequinsArray.push(new Sequin());
            }
        }

        // Confetti Class
        class Confetti {
            constructor() {
                this.x = Math.random() * canvas.width;
                this.y = Math.random() * canvas.height - canvas.height;
                this.size = Math.random() * 10 + 5;
                this.weight = Math.random() * 0.5 + 0.5;
                this.velocity = Math.random() * 5 + 1;
                this.angle = Math.random() * 2 * Math.PI;
                this.drag = dragConfetti;
                this.gravity = gravityConfetti;
                this.color = colors[Math.floor(Math.random() * colors.length)];
            }
            draw() {
                context.beginPath();
                context.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                context.fillStyle = this.color;
                context.fill();
                context.closePath();
            }
            update() {
                this.x += Math.sin(this.angle) * this.velocity;
                this.y += this.weight;
                this.weight += this.gravity;
                this.velocity *= 1 - this.drag;
                if (this.y > canvas.height + 20) {
                    this.y = -20;
                }
            }
        }

        // Sequin Class
        class Sequin {
            constructor() {
                this.x = Math.random() * canvas.width;
                this.y = Math.random() * canvas.height - canvas.height;
                this.size = Math.random() * 5 + 5;
                this.weight = Math.random() * 0.5 + 0.5;
                this.velocity = Math.random() * 5 + 1;
                this.angle = Math.random() * 2 * Math.PI;
                this.drag = dragSequins;
                this.gravity = gravitySequins;
                this.color = colors[Math.floor(Math.random() * colors.length)];
            }
            draw() {
                context.beginPath();
                context.rect(this.x, this.y, this.size, this.size);
                context.fillStyle = this.color;
                context.fill();
                context.closePath();
            }
            update() {
                this.x += Math.sin(this.angle) * this.velocity;
                this.y += this.weight;
                this.weight += this.gravity;
                this.velocity *= 1 - this.drag;
                if (this.y > canvas.height + 20) {
                    this.y = -20;
                }
            }
        }

        // animate function
        function animate() {
            context.clearRect(0, 0, canvas.width, canvas.height);
            confettiArray.forEach(particle => {
                particle.update();
                particle.draw();
            });
            sequinsArray.forEach(particle => {
                particle.update();
                particle.draw();
            });
            requestAnimationFrame(animate);
        }

        // set initial canvas size and create particles
        setCanvasSize();
        createParticles();
        animate();

        // handle button click
        button.addEventListener('click', () => {
            if (!disabled) {
                disabled = true;
                button.classList.add('loading');
                setTimeout(() => {
                    button.classList.remove('loading');
                    button.classList.add('complete');
                    setTimeout(() => {
                        button.classList.remove('complete');
                        disabled = false;
                    }, 2000);
                }, 3000);
            }
        });

        // handle window resize
        window.addEventListener('resize', () => {
            setCanvasSize();
        });
    </script>
</body>
</html>
