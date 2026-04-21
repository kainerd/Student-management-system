</main>

<div id="modal-overlay" class="modal-overlay" onclick="closeModal()"></div>
<div id="modal" class="modal">
    <div class="modal-header">
        <h3 id="modal-title">Modal Title</h3>
        <button class="modal-close" onclick="closeModal()">&times;</button>
    </div>
    <div id="modal-body" class="modal-body"></div>
</div>

<div id="toast" class="toast"></div>

<script src="<?= (strpos($_SERVER['PHP_SELF'], '/pages/') !== false) ? '../' : '' ?>assets/js/main.js"></script>
</body>
</html>
