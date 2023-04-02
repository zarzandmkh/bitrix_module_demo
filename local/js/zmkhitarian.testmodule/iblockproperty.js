function iblockCustomProperty() {
    const iblockSelect = document.querySelector('.zm_iblockselect');

    if (!iblockSelect) {
        return true;
    }

    iblockSelect.onchange = () => {
        let iblockId = iblockSelect.value;

        BX.ajax.runAction('zmkhitarian:testmodule.api.IblockSection.getList', {
            data: {
                iblockId: iblockId
            }
        }).then((response) => {
                if (!response.data) {
                    return true;
                }

                document.querySelector('.zm_error')?.remove();
                document.querySelector('.zm_sectionselect')?.remove();

                if (response.data.length === 0 && iblockId) {
                    let errorNode = document.createElement('span')
                    errorNode.style.color = 'red';
                    errorNode.classList.add('zm_error');
                    errorNode.innerHTML = 'У выбранного инфоблока разделов нет';
                    iblockSelect.parentNode.insertBefore(errorNode, iblockSelect.nextSibling);
                    return true;
                }

                const propertyId = iblockSelect.dataset.propid;

                let sectionSelect = document.createElement('select');
                sectionSelect.classList.add('zm_sectionselect');
                sectionSelect.name = `PROP[${propertyId}]`

                let option;
                for (let i in response.data) {
                    option = document.createElement('option');
                    option.value = `${response.data[i].ID}`;
                    option.innerHTML = response.data[i].NAME;
                    sectionSelect.appendChild(option);
                }

                iblockSelect.parentNode.insertBefore(sectionSelect, iblockSelect.nextSibling);
            },
            (response) => {
                console.error(response.errors);
            });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    iblockCustomProperty();
})
