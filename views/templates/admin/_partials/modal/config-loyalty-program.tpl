<div class="modal fade" id="modalConfigProgram" tabindex="-1" role="dialog" aria-labelledby="modalConfigProgram">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <button id="newProperty" type="button" class="btn btn-primary">Nuevo</button>
            </div>
            <div class="modal-body">
                <div id="formProgramProperty" class="row">
                    <div class="col-md-8">
                        <form id="formAddPropertyProgram">
                            <input type="hidden" id="id_loyalty_property" name="id_loyalty" value=""> 
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-addon">Elemento</div>
                                    <select class="form-control" name="element" id="propElement">
                                        <option value="strong">Negrita</option>
                                        <option value="a">Link</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-addon">Texto</div>
                                    <input type="text" class="form-control" id="propText" name="propText" placeholder="Promoción">
                                </div>
                            </div>
                            <div class="propDivUrl">
                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-addon">URL</div>
                                        <input type="text" class="form-control" id="propUrl" name="propUrl" placeholder="/2050-ofertas">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-addon">Abrir en</div>
                                        <select class="form-control" name="propUrlTarget" id="propUrlTarget">
                                            <option value="_blank">Otra pestaña</option>
                                            <option value="_self">La misma pestaña</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <button class="btn btn-primary btn-lg" type="submit">Crear</button>
                        </form>
                        <hr/>
                    </div>
                    <div class="col-md-4">
                        <div class="panel">
                            <h3><i class="icon icon-eye-open"></i> {l s='Vista previa' mod='fl_loyalty'}</h3>
                            <div class="prop-preview"></div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <table id="tableProgramsProperties" class="table table-bordered">
                        <caption>Modificadores de texto de promociones del programa de lealtad</caption>
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Propiedad</th>
                                <th>Texto</th>
                                <th>Resultado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>