class LoyaltyPromotions {


    static getUrlAjaxController(action) {
        return loyalty_promotions_ajax_link + '&' + new URLSearchParams({
            ajax: true,
            action: action
        });
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
                reject('No se encontró en archivo Excel');
            }
        });
    }

    static async save(data) {
        return await fetch(this.getUrlAjaxController('save'), {
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

}

