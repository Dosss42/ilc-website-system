{{-- Microsoft Edge adds its own "show password" eye inside password boxes (only while the box is
     focused and has text). Where a box already has our own eye button right after it, hide Edge's
     so there aren't two. Boxes without our button keep Edge's. Include in <head>. --}}
<style>
    input[type="password"]:has(+ button)::-ms-reveal,
    input[type="password"]:has(+ button)::-ms-clear { display: none; }
</style>
