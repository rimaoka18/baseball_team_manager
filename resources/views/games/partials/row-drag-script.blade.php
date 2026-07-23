<script>
    (function () {
        const tbody = document.getElementById(@json($tbodyId));
        if (!tbody) return;

        let draggedRow = null;
        let draggedHandle = null;
        let pointerId = null;

        function rowAtPoint(x, y) {
            const el = document.elementFromPoint(x, y);
            const row = el && el.closest('tr');
            return (row && row.parentElement === tbody) ? row : null;
        }

        function endDrag() {
            if (draggedHandle && pointerId !== null && draggedHandle.hasPointerCapture(pointerId)) {
                draggedHandle.releasePointerCapture(pointerId);
            }
            if (draggedRow) {
                draggedRow.classList.remove('opacity-50', 'shadow-lg', 'relative', 'z-10');
            }
            draggedRow = null;
            draggedHandle = null;
            pointerId = null;
            tbody.querySelectorAll('.batting-order').forEach((cell, index) => {
                cell.textContent = index + 1;
            });
        }

        tbody.addEventListener('pointerdown', (e) => {
            const handle = e.target.closest('.drag-handle');
            if (!handle) return;

            draggedRow = handle.closest('tr');
            draggedHandle = handle;
            pointerId = e.pointerId;
            handle.setPointerCapture(pointerId);
            draggedRow.classList.add('opacity-50', 'shadow-lg', 'relative', 'z-10');
            e.preventDefault();
        });

        tbody.addEventListener('pointermove', (e) => {
            if (!draggedRow || e.pointerId !== pointerId) return;
            e.preventDefault();

            const targetRow = rowAtPoint(e.clientX, e.clientY);
            if (!targetRow || targetRow === draggedRow) return;

            const rect = targetRow.getBoundingClientRect();
            const isAfter = (e.clientY - rect.top) > rect.height / 2;
            tbody.insertBefore(draggedRow, isAfter ? targetRow.nextSibling : targetRow);
        });

        tbody.addEventListener('pointerup', (e) => {
            if (e.pointerId !== pointerId) return;
            endDrag();
        });

        tbody.addEventListener('pointercancel', (e) => {
            if (e.pointerId !== pointerId) return;
            endDrag();
        });
    })();
</script>
