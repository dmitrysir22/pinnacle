{{-- Этот файл не выводит ничего на экран, только JS --}}
<script>
    function setupShipperAutofill() {
        const nameField = document.querySelector('select[name="shipper_name"]');
        
        if (!nameField) return;

        nameField.addEventListener('change', function() {
            const selectedName = this.value;
            if (!selectedName) return;

            // Используем fetch для получения данных (стандарт браузера)
			fetch("{{ backpack_url('shipment/get-shipper-info') }}?name=" + encodeURIComponent(selectedName))
                .then(response => response.json())
                .then(data => {
                    if (data) {
                        // Находим поля по атрибуту name и заполняем их
                        document.querySelector('input[name="shipper_address1"]').value = data.address1 || '';
                        document.querySelector('input[name="shipper_city"]').value = data.city || '';
                        document.querySelector('input[name="shipper_country"]').value = data.country || '';
                    }
                })
                .catch(error => console.error('Error fetching shipper:', error));
        });
    }

    // Запускаем после загрузки страницы
    if (document.readyState === 'complete') {
        setupShipperAutofill();
    } else {
        window.addEventListener('load', setupShipperAutofill);
    }
</script>