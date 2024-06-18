<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drag and Drop with Touch Support</title>
    <style>
        ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
            width: 200px;
            min-height: 50px;
            border: 1px solid #ccc;
            margin-bottom: 10px;
        }
        li {
            margin: 5px;
            padding: 10px;
            border: 1px solid #ccc;
            background-color: #f9f9f9;
            cursor: pointer;
            position: relative;
        }
        .placeholder {
            background-color: #f0f0f0;
            border: 2px dashed #ccc;
            margin: 5px;
            height: 20px;
            visibility: hidden;
        }
    </style>
</head>
<body>
    <ul id="list1">
        <li draggable="true">Item 1</li>
        <li draggable="true">Item 2</li>
        <li draggable="true">Item 3</li>
    </ul>

    <ul id="list2">
        <li draggable="true">Item A</li>
        <li draggable="true">Item B</li>
        <li draggable="true">Item C</li>
    </ul>

    <script>
        const items = document.querySelectorAll('li');
        const lists = document.querySelectorAll('ul');
        let draggedItem = null;
        let touchOffsetX = 0;
        let touchOffsetY = 0;
        let placeholder = null;

        items.forEach(item => {
            item.addEventListener('dragstart', dragStart);
            item.addEventListener('dragover', dragOver);
            item.addEventListener('dragend', dragEnd);
            item.addEventListener('touchstart', touchStart, { passive: false });
            item.addEventListener('touchmove', touchMove, { passive: false });
            item.addEventListener('touchend', touchEnd, { passive: false });
        });

        lists.forEach(list => {
            list.addEventListener('dragover', dragOver);
            list.addEventListener('drop', drop);
        });

        function dragStart(e) {
            draggedItem = this;
            placeholder = document.createElement('li');
            placeholder.className = 'placeholder';
            draggedItem.parentNode.insertBefore(placeholder, draggedItem.nextSibling);
            // Ensure item is draggable immediately without hiding it
            e.dataTransfer.setData('text/plain', ''); 
        }

        function dragOver(e) {
            e.preventDefault();
            if (e.target.tagName === 'LI' && e.target !== placeholder) {
                const rect = e.target.getBoundingClientRect();
                const next = (e.clientY - rect.top) / (rect.bottom - rect.top) > 0.5;
                e.target.parentNode.insertBefore(placeholder, next && e.target.nextSibling || e.target);
            }
        }

        function dragEnd(e) {
            if (draggedItem) {
                draggedItem.style.display = 'block';
                placeholder.parentNode.insertBefore(draggedItem, placeholder);
                placeholder.remove();
                draggedItem = null;
            }
        }

        function drop(e) {
            e.preventDefault();
            if (draggedItem) {
                placeholder.parentNode.insertBefore(draggedItem, placeholder);
                placeholder.remove();
                draggedItem.style.display = 'block';
                draggedItem = null;
            }
        }

        function touchStart(e) {
            draggedItem = this;
            const touch = e.touches[0];
            const rect = draggedItem.getBoundingClientRect();
            touchOffsetX = touch.clientX - rect.left;
            touchOffsetY = touch.clientY - rect.top;
            placeholder = document.createElement('li');
            placeholder.className = 'placeholder';
            draggedItem.parentNode.insertBefore(placeholder, draggedItem.nextSibling);
            draggedItem.style.opacity = '0.5';
            draggedItem.style.position = 'fixed';
            draggedItem.style.left = `${rect.left}px`;
            draggedItem.style.top = `${rect.top}px`;
            draggedItem.style.zIndex = '1000';
        }

        function touchMove(e) {
            e.preventDefault();
            const touch = e.touches[0];
            draggedItem.style.left = `${touch.clientX - touchOffsetX}px`;
            draggedItem.style.top = `${touch.clientY - touchOffsetY}px`;

            const target = document.elementFromPoint(touch.clientX, touch.clientY);
            if (target && target.tagName === 'LI' && target !== placeholder) {
                const rect = target.getBoundingClientRect();
                const next = (touch.clientY - rect.top) / (rect.bottom - rect.top) > 0.5;
                target.parentNode.insertBefore(placeholder, next && target.nextSibling || target);
            }
        }

        function touchEnd(e) {
            draggedItem.style.opacity = '1';
            draggedItem.style.position = 'relative';
            draggedItem.style.zIndex = '0';
            draggedItem.style.left = '0px';
            draggedItem.style.top = '0px';
            if (draggedItem) {
                placeholder.parentNode.insertBefore(draggedItem, placeholder);
                placeholder.remove();
                draggedItem = null;
            }
        }
    </script>
</body>
</html>
