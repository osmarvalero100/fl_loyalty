{*
* 2007-2021 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2021 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<div class="loyalty-config">
	<div class="panel">
		<h3><i class="icon icon-credit-card"></i> {l s='Programas de lealtad' mod='fl_loyalty'}</h3>

		<div class="modal fade" id="modalLoyatyProgram" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
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
									<div class="form-group">
										<div class="input-group">
											<div class="input-group-addon">Elemento</div>
											<select class="form-control" name="propElement" id="propElement">
												<option value="a">Link</option>
												<option value="strong">Resaltar palabra o frase</option>
											</select>
										</div>
									</div>
									<div class="form-group">
										<div class="input-group">
											<div class="input-group-addon">Texto</div>
											<input type="text" class="form-control" id="propText" name="propText" placeholder="Ver todos los productos del programa Pfizer conmigo">
										</div>
									</div>
									<div class="propDivUrl">
										<div class="form-group">
											<div class="input-group">
												<div class="input-group-addon">URL</div>
												<input type="text" class="form-control" id="propUrl" name="propUrl" placeholder="/2880-pfizer">
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
							<table class="table table-bordered">
								<caption>Modificadores de texto de promociones del programa de lealtad</caption>
								<thead>
									<tr>
										<th>Id</th>
										<th>Programa</th>
										<th>Descripción</th>
										<th>Acciones</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<th scope="row">1</th>
										<td>Mark</td>
										<td>
											Lorem ipsum dolor sit amet consectetur, adipisicing elit. Voluptatum iste minus nulla fugit nam in laborum expedita provident maxime? 
										</td>
										<td>
											<div class="row">
												<div class="col-md-3"><i class="icon icon-trash"></i></div>
											</div>
										</td>
									</tr>
									<tr>
										<th scope="row">2</th>
										<td>Jacob</td>
										<td>Thornton</td>
										<td>---</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-8">
				<div class="table-responsive" style="height: 200px;overflow-y: auto;">
					<table id="tablePrograms" class="table table-bordered">
						<thead>
							<tr>
								<th>Id</th>
								<th>Programa</th>
								<th>Descripción</th>
								<th>Estado</th>
								<th>Acciones</th>
							</tr>
						</thead>
						<tbody>
							{if !empty($programs)}
								{foreach from=$programs item=program}
									<tr class="program-{$program.id_loyalty}">
										<td>{$program.id_loyalty}</td>
										<td>{$program.name}</td>
										<td>{$program.description}</td>
										<td>
											<i class="material-icons action-{if $program.active}enabled{else}disabled{/if}">{if $program.active}check{else}clear{/if}</i>
										</td>
										<td>
											<div class="row loyalty-programs-actions">
												<div class="col-md-3"><i data-id-program="{$program.id_loyalty}" title="Ajustes" class="icon icon-cog"></i></div>
												<div class="col-md-3"><i data-id-program="{$program.id_loyalty}" title="Borras todas la promociones de este programa" class="icon icon-remove-circle"></i></div>
												<div class="col-md-3"><i data-id-program="{$program.id_loyalty}" title="Editar" class="icon icon-edit"></i></div>
												<div class="col-md-3"><i data-id-program="{$program.id_loyalty}" title="Eliminar" class="icon icon-trash"></i></div>
											</div>
										</td>
									</tr>
								{/foreach}
							{/if}
						</tbody>
					</table>
				</div>
			</div>
			<div class="col-md-4">
				<div class="panel">
					<h3><i class="icon icon-plus"></i> {l s='Agregar Programa de lealtad' mod='fl_loyalty'}</h3>
					<form id="loyalty" action="{$module_dir}ajax.php" method="post">
						<input type="hidden" id="id_loyalty" name="id_loyalty" value=""> 
						<div class="form-group">
							<input type="text" class="form-control" id="name" name="name" placeholder="Nombre del programa de lealtad">
						</div>
						<div class="form-group">
							<input type="text" class="form-control" id="description" name="description" placeholder="Descripción del programa de lealtad">
						</div>

						<button type="reset" class="btn btn-lg btn-block btn-clear" onclick="clearFormLoyanty()">Cancelar</button>
						<button type="submit" class="btn btn-primary btn-lg btn-block">Crear</button>
					</form>
				</div>
			</div>
		</div>
	</div>

	<div class="panel">
		<h3><i class="icon icon-gift"></i> {l s='Promociones' mod='fl_loyalty'}</h3>
		<div class="row">
			<div class="col-md-8">
				<form id="uploadAjaxPromotions" enctype="multipart/form-data" method="post" action="{$module_dir}ajax.php">
					<div class="form-group">
						<select class="form-control" name="uploadProgram" id="uploadProgram">
							<option value="">Seleccione un programa</option>
							{if !empty($programs)}
								{foreach from=$programs item=program}
									<option value="">{$program.name}</option>
								{/foreach}
							{/if}
							
						</select>
					</div>
					
					<div class="form-group">
						<input class="form-control"  type="file" id="promotions" accept=".xls,.xlsx" name="promotions"/>
					</div>
					<button class="btn btn-primary btn-lg" type="submit">Subir promociones</button>
				</form>
			</div>
			<div class="col-md-4">
				<div class="panel">
					<h3><i class="icon icon-eye-open"></i> {l s='Vista previa' mod='fl_loyalty'}</h3>
				</div>
			</div>
		</div>
	</div>
</div>