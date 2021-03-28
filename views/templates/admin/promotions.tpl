<div class="panel">
    <h3><i class="icon icon-gift"></i> {l s='Promociones' mod='fl_loyalty'}</h3>
    <div class="row">
        <div class="col-md-8">
            <form id="uploadAjaxPromotions" enctype="multipart/form-data" method="post" action="{$module_dir}ajax.php">
                <div class="form-group">
                    <select class="form-control" name="uploadProgram" id="uploadProgram" required>
                        <option value="">Seleccione un programa</option>
                        {if !empty($programs)}
                            {foreach from=$programs item=program}
                                <option value="{$program.id_loyalty}">{$program.name}</option>
                            {/foreach}
                        {/if}
                        
                    </select>
                </div>
                
                <div class="form-group">
                    <input class="form-control"  type="file" id="promotions" accept=".xls,.xlsx" name="promotions" required/>
                </div>

                <div class="form-group">
                    <button class="btn btn-primary btn-lg" type="submit">Subir promociones</button>
                </div>

                <div class="row">
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" aria-valuenow="1" aria-valuemin="0" aria-valuemax="100" style="min-width: 2em; width: 0%;">
                            0%
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-4">
            <div class="panel">
                <h3><i class="icon icon-eye-open"></i> {l s='Vista previa' mod='fl_loyalty'}</h3>
                <div id="promotionPreview"></div>
            </div>
        </div>
    </div>
</div>