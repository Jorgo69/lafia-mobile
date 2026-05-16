import './bootstrap';

// --- Audio Guide ---
let currentAudio = null;
let currentUtterance = null;

window.playAudioGuide = function(button) {
    // Stop any currently playing audio
    if (currentAudio) {
        currentAudio.pause();
        currentAudio = null;
    }
    if (currentUtterance) {
        window.speechSynthesis.cancel();
        currentUtterance = null;
    }

    const audioPath = button.dataset.audioPath;
    const text = button.dataset.audioText;

    // Try mp3 file first (offline-ready)
    if (audioPath) {
        currentAudio = new Audio(audioPath);
        currentAudio.play().catch(() => {
            // Fallback to speech synthesis
            speakText(text);
        });
        return;
    }

    // Fallback: Web Speech API
    speakText(text);
};

function speakText(text) {
    if (!('speechSynthesis' in window) || !text) return;

    const utterance = new SpeechSynthesisUtterance(text);
    utterance.lang = 'fr-FR';
    utterance.rate = 0.85;
    utterance.pitch = 1;

    currentUtterance = utterance;
    window.speechSynthesis.speak(utterance);
}
