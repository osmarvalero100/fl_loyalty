class LoyaltyPromotions {


    static getUrlAjaxController(action) {
        return loyalty_promotions_ajax_link + '&' + new URLSearchParams({
            ajax: true,
            action: action
        });
    }

    static async getPromotionByLoyalty(idLoyalty) {
        return await fetch(this.getUrlAjaxController('getPromotionByLoyalty') + '&' + new URLSearchParams({
            id_loyalty: idLoyalty,
        }));
    }

    static convertExcelToJson(excelFile) {
        let jsonData;

        return new Promise((resolve, reject) => {
            if (excelFile) {
                let fileReader = new FileReader();
                fileReader.readAsBinaryString(excelFile);
                fileReader.onload = (event) => {
                    let data =  event.target.result;
                    let workbook = XLSX.read(data, {type: 'binary'});
                    workbook.SheetNames.forEach(sheet => {
                        jsonData =  XLSX.utils.sheet_to_row_object_array(workbook.Sheets[sheet]);
                        
                        resolve(jsonData);
                    });
                }
            } else {
                reject('No se encontró el archivo Excel');
            }
        });
    }

    static async save(data) {
        return await fetch(this.getUrlAjaxController('save'), {
            method: 'POST',
            body: data
        });
    }

    static async delete(data) {
        return await fetch(this.getUrlAjaxController('delete'), {
            method: 'POST',
            body: data
        });
    }

    static async deleteByLoyalty(data) {
        return await fetch(this.getUrlAjaxController('deleteByLoyalty'), {
            method: 'POST',
            body: data
        });
    }

    static renderPromotionPreview(idLoyalty) {
        Loyalty.getById(idLoyalty)
        .then(res => res.json())
        .then(res => {
            if (res.html_tags != '') {
                const loyaltyproperties = JSON.parse(res.html_tags);

                if (loyaltyproperties.length) {
                    let text = '';

                    loyaltyproperties.forEach(element => {
                        if (element.element == 'strong')
                            text += `Aut minus <strong>${element.text}</strong> quia.`;
                        if (element.element == 'a')
                            text += `Est sequi illo alias quaerat maiores veritatis rerum non est <a title="${element.text}" href="${element.url}" target="${element.target}">${element.text}</a>.`;
                    });

                    document.getElementById('promotionPreview').innerHTML = text;
                }
            } else {
                document.getElementById('promotionPreview').innerHTML = 'Aut minus quia. Est sequi illo alias quaerat maiores veritatis rerum non est.';
            }
        })
        .catch(error => console.log('Error:', error));
    }

    static renderListPromotions(promotions) {
        const listPromotions = document.getElementById('listPromotions');
        promotions.forEach(promo => {
            const rowPromo = document.createElement('div');
            rowPromo.classList.add('row');
            rowPromo.classList.add('list-group-item');
            rowPromo.classList.add(`promotion-${promo.id_loyalty_promotion}`)
            rowPromo.innerHTML = `<div class="col-md-11">
                    <p><span class="badge"><strong>${promo.promotion}</strong></span>  ${promo.description}</p>
                </div>
                <div class="col-md-1 text-right">
                    <i onclick="deletePromotion(${promo.id_loyalty_promotion})" title="Eliminar" class="icon icon-trash"></i>
                </div>`;
            listPromotions.appendChild(rowPromo);
        });
    }

}

