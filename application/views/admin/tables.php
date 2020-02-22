<?php $this->load->view('admin/nav_menu'); ?>

<div class="container">

	<div class="row">
		<div class="col-md-12">
			<ol class="breadcrumb">
				<li><a href="<?php echo site_url('admin/tables'); ?>">Root</a></li>
				<?php
				if($select_table != 'root')
				echo "<li class='active'><a href='".site_url('admin/tables/'.$select_table)."'>".$select_table."</a></li>";
				?>
			</ol>
		</div>

		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Tables de DAPHNE</h3>
				</div>
				<div class="panel-body">

					<div class="col-md-2">
						<form action="<?php echo site_url('admin/tables/'.$select_table); ?>" method="post">
							<div class="input-group">
								<input type="text" class="form-control" name="nb_elements" value="<?php echo $nb_elements; ?>" placeholder="1">
								<span class="input-group-btn">
									<button class="btn btn-default" type="submit">par page</button>
								</span>
							</div><!-- /input-group -->
						</form>
					</div>

					<div class="col-md-8">
						<?php  echo $this->pagination->create_links(); ?>
					</div>

					<div class="col-md-2">
						<form action="<?php echo site_url('admin/tables').'/'.$select_table; ?>" method="post">
							<div class="input-group">
								<span class="input-group-btn">
									<button class="btn btn-default" type="submit">Aller à</button>
								</span>
								<input type="text" class="form-control" name="go_to" value="<?php if(isset($go_to)) echo $go_to;?>" placeholder="#">
							</div><!-- /input-group -->
						</form>
					</div>

				</div>
				<div class="db_table"><?php echo $tables_html; ?></div>
				<div class="panel-footer">
					<?php  echo $this->pagination->create_links(); ?>
				</div>
			</div>
		</div>
	</div>
</div>
