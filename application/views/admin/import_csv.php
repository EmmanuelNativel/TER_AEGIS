<?php $this->load->view('admin/nav_menu'); ?>

<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Import CSV</h3>
				</div>
				<div class="panel-body">
					<?php echo form_open_multipart('admin/import_csv','class="contact-form row"');?>

					<div class="col-md-4"></div>
					<div class="col-md-4">

						<p>
							<label class="control-label">Sélectionnez un fichier</label>
							<input id="userfile" name="userfile" type="file" class="filestyle">
						</p>
						<?php
						if(isset($error))
						echo '<div class="alert alert-danger" role="alert">'.$error.'</div>';
						?>

						<p>
							<label for="select-table">Sélectionnez une table:</label>
							<select id="select-table" name="table" placeholder="Aucune table sélectionnée" requierd>
								<option value="">Aucune table sélectionnée</option>
								<?php
								foreach ($list_tables as $table) {
									echo '<option value="'.$table.'">'.$table."</option>";
								}
								?>
							</select>
						</p>
						<?php
						if(form_error('table') != NULL)
						echo '<div class="alert alert-danger" role="alert">'.form_error('table'). '</div>';
						?>

						<p>
							<button id="config" type="button">
								<span class="glyphicon glyphicon-cog" aria-hidden="true"></span> Avancés
							</button>
						</p>

						<span id="config_elements">

							<p>
								<label class="control-label">Délimiteur</label>
								<input id="delimiter" name="delimiter" value=";" type="text" size="1" placeholder='(exemple: ";" "," "\t" ":" "|" etc..)'>

							</p>

							<p>
								<input id="checkbox-header" type="checkbox" checked value="TRUE" name="header"/>
								<label for="checkbox-header">
									<span class="ui"></span>En-tête incluse
								</label>
							</p>

						</span>

						<p class="text-right">
							<input type="submit" value="Importer">
						</p>

						<?php
						if(isset($success))
						echo '<div class="alert alert-success" role="alert">Téléchargement réussi!</div>';
						?>

					</div>
					<div class="col-md-4"></div>

				</form>
			</div>
		</div>
	</div>
</div>
</div>
