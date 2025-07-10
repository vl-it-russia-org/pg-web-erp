// PostReq.js
// Открывает отдельное окно с заданными размерами и отправляет туда POST-форму

function openFormWithPostInWindow(formName, postParams, winName) {
    // Открываем новое окно с нужными параметрами
    window.open('', winName, 'width=300,height=400,left=50,top=50,resizable=yes,scrollbars=yes');

    // Создаем форму
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = formName;
    form.target = winName;

    // Добавляем параметры как скрытые поля
    postParams.forEach(([name, value]) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        form.appendChild(input);
    });

    // Добавляем форму на страницу, отправляем и удаляем
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

// Пример использования по кнопке:
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.createElement('button');
    btn.textContent = 'Открыть форму POST в отдельном окне';
    btn.onclick = function() {
        openFormWithPostInWindow('SecondFrm.php', [['Param1', 1000], ['Param2', 'second param']], 'MyCustomWin');
    };
    document.body.appendChild(btn);
}); 