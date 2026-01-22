                    document.addEventListener('DOMContentLoaded', function() {
                        // ID роли агента из PHP
                        const agentRoleId = '2'; 
                        
                        // Находим все чекбоксы ролей (в Backpack они обычно name='roles_show[]')
                        const roleCheckboxes = document.querySelectorAll('input[name=\"roles_show[]\"]');
                        
                        // Находим поля, которые нужно прятать (по классу, который мы дали выше)
                        const agentFields = document.querySelectorAll('.agent-dependent-field');

                        function toggleAgentFields() {
                            let isAgentSelected = false;

                            // Проверяем, отмечена ли роль AgentUser
                            roleCheckboxes.forEach(cb => {
                                if (cb.value == agentRoleId && cb.checked) {
                                    isAgentSelected = true;
                                }
                            });

                            agentFields.forEach(field => {
                                const input = field.querySelector('select, input');
                                
                                if (isAgentSelected) {
                                    // ПОКАЗАТЬ
                                    field.style.display = 'block';
                                    // Сделать обязательным (браузерная проверка)
                                    if(input) input.setAttribute('required', 'required');
                                } else {
                                    // СКРЫТЬ
                                    field.style.display = 'none';
                                    // Убрать обязательность, иначе форма не отправится
                                    if(input) input.removeAttribute('required');
                                    // Опционально: очистить значение при скрытии
                                    // if(input) input.value = ''; 
                                }
                            });
                        }

                        // Вешаем обработчик на клики
                        roleCheckboxes.forEach(cb => {
                            cb.addEventListener('change', toggleAgentFields);
                        });

                        // Запускаем один раз при загрузке страницы (для редактирования)
                        toggleAgentFields();
                    });

document.addEventListener("DOMContentLoaded", function() {
    // 1. Внедрение подсказки в роли
    const roleContainer = document.querySelector('[bp-field-name="roles,permissions"]');
    if (roleContainer) {
        const rolesLabel = Array.from(roleContainer.querySelectorAll("label")).find(el => el.textContent.trim() === "Roles");
        
        if (rolesLabel && !document.querySelector('.role-explanation')) {
            const infoDiv = document.createElement("div");
            infoDiv.className = "role-explanation";
            infoDiv.innerHTML = `
                <strong>User Type Guide:</strong><br>
                <span class="d-block mt-1 text-muted"><b>Admin:</b> For Pinnacle staff (internal management).</span>
                <span class="d-block text-muted"><b>AgentUser:</b> For customers (frontend portal access).</span>
            `;
            rolesLabel.parentNode.insertBefore(infoDiv, rolesLabel.nextSibling);
        }
    }
	
	$('[bp-field-name="roles,permissions"] .container').removeClass('container');
	$('[bp-field-name="roles,permissions"] .col-sm-4').removeClass('col-sm-4');
	$('[bp-field-name="is_approved"]').insertBefore('[bp-field-name="name"]');

});