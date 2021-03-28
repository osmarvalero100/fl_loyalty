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
	{include file='./_partials/modal/config-loyalty-program.tpl'}

	<div class="panel">
		<h3><i class="icon icon-credit-card"></i> {l s='Programas de lealtad' mod='fl_loyalty'}</h3>
		
		<div class="row">
			<div class="col-md-8">
				<div class="table-responsive" style="height: 200px;overflow-y: auto;">
					<table id="tablePrograms" class="table table-bordered">
						<thead>
							<tr>
								<th>Id</th>
								<th>Programa</th>
								<th>Descripción</th>
								<th>Fecha de caducidad</th>
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
										<td>{$program.date_end}</td>
										<td>
											<i onclick="changeStatus({$program.id_loyalty})" class="material-icons action-{if $program.active}enabled{else}disabled{/if}" title="{if $program.active}Desactivar{else}Activar{/if}">
												{if $program.active}check{else}clear{/if}
											</i>
										</td>
										<td>
											<div class="row loyalty-programs-actions">
												<div class="col-md-3"><i onclick="listPromotions({$program.id_loyalty})" title="Ver las promociones de este programa" class="icon icon-eye-open"></i></div>
												<div class="col-md-3"><i onclick="configLoyalty({$program.id_loyalty})" title="Ajustes" class="icon icon-cog"></i></div>
												<div class="col-md-2"><i onclick="deletePromotionsByLoyalty({$program.id_loyalty})" title="Borrar todas las promociones de este programa" class="icon icon-remove-circle"></i></div>
												<div class="col-md-2"><i onclick="getDataProgram({$program.id_loyalty})" title="Editar" class="icon icon-edit"></i></div>
												<div class="col-md-2"><i onclick="deleteProgram({$program.id_loyalty})" title="Eliminar" class="icon icon-trash"></i></div>
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
							<input type="text" class="form-control" id="name" name="name" placeholder="Nombre del programa de lealtad" required>
						</div>
						<div class="form-group">
							<input type="text" class="form-control" id="description" name="description" placeholder="Descripción del programa de lealtad" required>
						</div>
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">Fecha de caducidad</div>
								<input type="text" id="date_end" name="date_end" class="datepicker form-control" placeholder="YYYY-mm-dd hh:mm:ss">
							</div>
						</div>
						<button id="btnLoyaltyReset" type="reset" class="btn btn-lg btn-block btn-clear">Cancelar</button>
						<button id="btnLoyaltySubmit" type="submit" class="btn btn-primary btn-lg btn-block">Crear</button>
					</form>
				</div>
			</div>
		</div>
	</div>

	{include file='./promotions.tpl'}
	
	{include file='./_partials/modal/list-promotions.tpl'}

</div>
