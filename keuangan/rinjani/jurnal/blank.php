<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Simple Flip Style Clock</title>
    <style>
        body {
            background: #fff;
            color: #666;
            font-family: "Courier New", Courier, monospace;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .clock {
            display: flex;
            gap: 15px;
        }

        .digit {
            width: 100px;
            height: 140px;
            background: #dedede;
            color: #efefef;
            font-size: 5rem;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.4);
            position: relative;
        }

        .digit::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 0;
            width: 100%;
            height: 2px;
            background: #ccc;
            opacity: 0.6;
        }

        .colon {
            width: 40px;
            height: 140px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 5rem;
            font-weight: bold;
            color: #efefef;
        }

        .date {
            margin-top: 30px;
            font-size: 1.5rem;
            color: #666;
        }

        .top-bar {
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            /* optional styling */
            background: #eee;
            padding: 10px 20px;
        }
    </style>
</head>
<body>
<?php
if (isset($_REQUEST["showwait"])) { ?>
    <div class="top-bar"> memuat ..</div>
    <?php
}
?>

<div class="clock">
    <div class="digit" id="hourTens">0</div>
    <div class="digit" id="hourOnes">0</div>
    <div class="colon">:</div>
    <div class="digit" id="minuteTens">0</div>
    <div class="digit" id="minuteOnes">0</div>
    <div class="colon">:</div>
    <div class="digit" id="secondTens">0</div>
    <div class="digit" id="secondOnes">0</div>
</div>

<div class="date" id="date"></div>

<script>
    function pad(num) {
        return num.toString().padStart(2, '0');
    }

    function updateClock() {
        const now = new Date();
        const h = pad(now.getHours());
        const m = pad(now.getMinutes());
        const s = pad(now.getSeconds());

        document.getElementById("hourTens").textContent = h[0];
        document.getElementById("hourOnes").textContent = h[1];
        document.getElementById("minuteTens").textContent = m[0];
        document.getElementById("minuteOnes").textContent = m[1];
        document.getElementById("secondTens").textContent = s[0];
        document.getElementById("secondOnes").textContent = s[1];

        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        document.getElementById("date").textContent = now.toLocaleDateString("id-ID", options);
    }

    setInterval(updateClock, 1000);
    updateClock();
</script>
</body>
</html>
