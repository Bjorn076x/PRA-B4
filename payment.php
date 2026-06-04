<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Betaling gelukt</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, #0d0d2b 0%, #0a0a3a 50%, #0d1b4a 100%);
      font-family: system-ui, -apple-system, sans-serif;
    }

    .scene {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 1.25rem;
      padding: 3rem 2rem;
      text-align: center;
    }

    /* ── Check circle ── */
    @keyframes checkPop {
      0%   { transform: scale(0) rotate(-20deg); opacity: 0; }
      60%  { transform: scale(1.2) rotate(5deg);  opacity: 1; }
      100% { transform: scale(1)   rotate(0deg);  opacity: 1; }
    }
    @keyframes ringPulse {
      0%, 100% { box-shadow: 0 0 0 0   rgba(52,211,153,.45); }
      50%       { box-shadow: 0 0 0 20px rgba(52,211,153,0);  }
    }
    @keyframes wrapFade {
      from { opacity: 0; transform: translateY(24px); }
      to   { opacity: 1; transform: translateY(0);    }
    }

    .check-wrap {
      animation: wrapFade .5s cubic-bezier(.34,1.56,.64,1) .1s both;
    }
    .check-circle {
      width: 72px; height: 72px;
      border-radius: 50%;
      background: linear-gradient(135deg, #10b981, #059669);
      display: flex; align-items: center; justify-content: center;
      animation: ringPulse 2s ease-in-out .8s infinite;
    }
    .check-icon {
      animation: checkPop .5s cubic-bezier(.34,1.56,.64,1) .3s both;
    }
    .check-icon svg {
      width: 36px; height: 36px;
      stroke: #fff; stroke-width: 3;
      fill: none; stroke-linecap: round; stroke-linejoin: round;
    }

    /* ── Headline ── */
    @keyframes wordReveal {
      from { opacity: 0; transform: translateY(12px); filter: blur(4px); }
      to   { opacity: 1; transform: translateY(0);    filter: blur(0);   }
    }
    @keyframes shimmer {
      from { background-position: -400px 0; }
      to   { background-position:  400px 0; }
    }
    @keyframes underlineGrow {
      from { width: 0; }
      to   { width: 100%; }
    }

    .headline {
      position: relative;
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 0 10px;
    }
    .word {
      font-size: clamp(22px, 3.5vw, 32px);
      font-weight: 600;
      color: #ffffff;
      opacity: 0;
      animation: wordReveal .5s ease forwards;
      display: inline-block;
    }
    .word:nth-child(1) { animation-delay: .55s; }
    .word:nth-child(2) { animation-delay: .75s; }
    .word:nth-child(3) { animation-delay: .95s; }
    .word:nth-child(4) { animation-delay: 1.15s; }
    .word:nth-child(5) { animation-delay: 1.35s; }
    .word:nth-child(6) { animation-delay: 1.55s; }
    .word:nth-child(7) { animation-delay: 1.75s; }
    .word:nth-child(8) { animation-delay: 1.95s; }

    .word.highlight {
      background: linear-gradient(90deg, #10b981, #34d399, #6ee7b7, #34d399, #10b981);
      background-size: 200% auto;
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      animation: wordReveal .5s ease forwards, shimmer 3s linear 2.5s infinite;
    }

    .underline-bar {
      position: absolute;
      bottom: -6px; left: 50%;
      transform: translateX(-50%);
      height: 2px;
      background: linear-gradient(90deg, transparent, #10b981, transparent);
      border-radius: 2px;
      width: 0;
      animation: underlineGrow .7s ease 2.2s forwards;
    }

    /* ── Subtitle ── */
    @keyframes subtitleFade {
      from { opacity: 0; letter-spacing: .3em; }
      to   { opacity: 1; letter-spacing: .08em; }
    }
    .subtitle {
      font-size: 13px;
      font-weight: 400;
      color: rgba(255,255,255,.45);
      letter-spacing: .3em;
      text-transform: uppercase;
      opacity: 0;
      animation: subtitleFade .8s ease 2.5s forwards;
    }

    /* ── Replay button ── */
    @keyframes btnFade {
      from { opacity: 0; transform: translateY(8px); }
      to   { opacity: 1; transform: translateY(0);   }
    }
    .replay-btn {
      margin-top: .5rem;
      font-size: 13px;
      color: rgba(255,255,255,.5);
      background: rgba(255,255,255,.06);
      border: 1px solid rgba(255,255,255,.15);
      border-radius: 8px;
      padding: 7px 18px;
      cursor: pointer;
      transition: background .2s, color .2s;
      opacity: 0;
      animation: btnFade .5s ease 3.2s forwards;
    }
    .replay-btn:hover {
      background: rgba(255,255,255,.14);
      color: rgba(255,255,255,.9);
    }
  </style>
</head>
<body>
  <div class="scene" id="scene">

    <div class="check-wrap">
      <div class="check-circle">
        <div class="check-icon">
          <svg viewBox="0 0 24 24" aria-hidden="true">
            <polyline points="20 6 9 17 4 12"/>
          </svg>
        </div>
      </div>
    </div>

    <div class="headline">
      <span class="word highlight">Betaling</span>
      <span class="word highlight">gelukt!</span>
      <span class="word">Bedankt</span>
      <span class="word">voor</span>
      <span class="word">uw</span>
      <span class="word">aankoop</span>
      <span class="word">bij</span>
      <span class="word">onze&nbsp;foto-koisk.</span>
      <div class="underline-bar"></div>
    </div>

    <p class="subtitle">Transactie voltooid</p>

    

  </div>

  <script>
    document.getElementById('replayBtn').addEventListener('click', function () {
      const scene = document.getElementById('scene');
      const html = scene.innerHTML;
      scene.innerHTML = html;
      document.getElementById('replayBtn').addEventListener('click', arguments.callee);
    });
  </script>
</body>
</html>