import confetti from 'canvas-confetti';

// =========================================================================
// OFFLINE WEB AUDIO SYNTHESIZER (No external mp3 files needed)
// =========================================================================
let audioCtx = null;
let isAudioMuted = false;

function getAudioContext() {
    if (!audioCtx) {
        const AudioContext = window.AudioContext || window.webkitAudioContext;
        if (AudioContext) {
            audioCtx = new AudioContext();
        }
    }
    if (audioCtx && audioCtx.state === 'suspended') {
        audioCtx.resume();
    }
    return audioCtx;
}

// Play rapid mechanical tick/percussion for rolling state
export function playTickSound() {
    if (isAudioMuted) return;
    try {
        const ctx = getAudioContext();
        if (!ctx) return;

        const osc = ctx.createOscillator();
        const gain = ctx.createGain();

        // Short click sound with pitch drop
        const now = ctx.currentTime;
        osc.type = 'triangle';
        osc.frequency.setValueAtTime(450 + Math.random() * 200, now);
        osc.frequency.exponentialRampToValueAtTime(80, now + 0.04);

        gain.gain.setValueAtTime(0.25, now);
        gain.gain.exponentialRampToValueAtTime(0.001, now + 0.04);

        osc.connect(gain);
        gain.connect(ctx.destination);

        osc.start(now);
        osc.stop(now + 0.05);
    } catch (e) {
        // Ignore audio errors if blocked by browser policy
    }
}

// Play celebratory fanfare chords on victory
export function playFanfareSound() {
    if (isAudioMuted) return;
    try {
        const ctx = getAudioContext();
        if (!ctx) return;

        const now = ctx.currentTime;
        // Chords sequence: C4, E4, G4, C5 (Majestic Arpeggio + Sustained Grand Chord)
        const notes = [
            { freq: 261.63, time: 0.00, dur: 0.18 }, // C4
            { freq: 329.63, time: 0.15, dur: 0.18 }, // E4
            { freq: 392.00, time: 0.30, dur: 0.18 }, // G4
            { freq: 523.25, time: 0.45, dur: 1.60 }, // C5
            { freq: 659.25, time: 0.50, dur: 1.55 }, // E5
            { freq: 783.99, time: 0.55, dur: 1.50 }, // G5
            { freq: 1046.50, time: 0.60, dur: 1.80 }, // C6
        ];

        notes.forEach(({ freq, time, dur }) => {
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();

            osc.type = 'sawtooth';
            osc.frequency.setValueAtTime(freq, now + time);

            // Brassy envelope with gentle decay
            gain.gain.setValueAtTime(0.001, now + time);
            gain.gain.linearRampToValueAtTime(0.18, now + time + 0.04);
            gain.gain.exponentialRampToValueAtTime(0.001, now + time + dur);

            osc.connect(gain);
            gain.connect(ctx.destination);

            osc.start(now + time);
            osc.stop(now + time + dur);
        });
    } catch (e) {
        // Ignore audio errors
    }
}

// =========================================================================
// CANVAS CONFETTI SPECTACULAR ENGINE
// =========================================================================
export function launchConfetti() {
    const count = 200;
    const defaults = {
        origin: { y: 0.7 },
        zIndex: 9999,
    };

    function fire(particleRatio, opts) {
        confetti({
            ...defaults,
            ...opts,
            particleCount: Math.floor(count * particleRatio),
        });
    }

    // Stage 1: Fireworks blast from center
    fire(0.25, {
        spread: 26,
        startVelocity: 55,
        colors: ['#f59e0b', '#fbbf24', '#10b981', '#6366f1', '#ec4899'],
    });
    fire(0.2, {
        spread: 60,
        colors: ['#f59e0b', '#fbbf24', '#ffffff', '#e11d48'],
    });
    fire(0.35, {
        spread: 100,
        decay: 0.91,
        scalar: 0.8,
        colors: ['#10b981', '#3b82f6', '#fbbf24'],
    });
    fire(0.1, {
        spread: 120,
        startVelocity: 25,
        decay: 0.92,
        scalar: 1.2,
    });
    fire(0.1, {
        spread: 120,
        startVelocity: 45,
    });

    // Stage 2: Left & Right Cannons after 300ms
    setTimeout(() => {
        confetti({
            particleCount: 80,
            angle: 60,
            spread: 55,
            origin: { x: 0, y: 0.75 },
            colors: ['#f59e0b', '#fbbf24', '#10b981'],
            zIndex: 9999,
        });
        confetti({
            particleCount: 80,
            angle: 120,
            spread: 55,
            origin: { x: 1, y: 0.75 },
            colors: ['#6366f1', '#ec4899', '#3b82f6'],
            zIndex: 9999,
        });
    }, 300);
}

// Expose globally for Alpine.js
window.playTickSound = playTickSound;
window.playFanfareSound = playFanfareSound;
window.launchConfetti = launchConfetti;
window.unlockAudioContext = getAudioContext;
window.toggleAudioMute = () => {
    isAudioMuted = !isAudioMuted;
    return isAudioMuted;
};
