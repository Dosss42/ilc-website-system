/**
 * Name boxes marked data-capitalize="words": the first letter of each word becomes a capital while typing
 * ("maria dela cruz" → "Maria Dela Cruz", "o'connell" → "O'Connell"). The rest of each word is kept as typed,
 * so names like "McDonald" or "deGuzman" are not lower-cased. The cursor stays where it was.
 * Server side: App\Support\NameCase::words() applies the same rule when the name is saved.
 */
(function () {
    function capitalizeWords(text) {
        return text.replace(/(^|[\s\-'’])(\p{Ll})/gu, function (m, before, letter) { return before + letter.toUpperCase(); });
    }
    document.addEventListener('input', function (e) {
        var el = e.target;
        if (!el || !el.matches || !el.matches('[data-capitalize="words"]')) return;
        var next = capitalizeWords(el.value);
        if (next === el.value) return;
        var start = el.selectionStart, end = el.selectionEnd; // same length, so the cursor can stay put
        el.value = next;
        try { el.setSelectionRange(start, end); } catch (err) {}
    });
    window.ilcCapitalizeWords = capitalizeWords;
})();
