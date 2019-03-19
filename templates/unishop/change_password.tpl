<div class="content">
	<div class="content__container">

		<div class="content__header">
			<h1 class="content__title">
				{tlang('Change Password')}
			</h1>
		</div>
		<div class="content__row">
			<div class="row">
				<div class="col-sm-7">
					<form action="{site_url('auth/change_password')}" method="POST">

						{if $info_message}
						<div class="form__messages">
							<p class="message message--error">{$info_message}</p>
						</div>
						{/if}
						
						<!-- Name field -->
						{view('includes/forms/input-base.tpl', [
							'label' => tlang('Current password'),
							'type' => 'password',
							'name' => 'old_password',
							'value' => "",
							'required' => true
						])}

						<!-- Name field -->
						{view('includes/forms/input-base.tpl', [
							'label' => tlang('New password'),
							'type' => 'password',
							'name' => 'new_password',
							'value' => "",
							'required' => true
						])}

						<!-- Name field -->
						{view('includes/forms/input-base.tpl', [
							'label' => tlang('Reenter new password'),
							'type' => 'password',
							'name' => 'confirm_new_password',
							'value' => "",
							'required' => true
						])}

						<!-- Submit button -->
						<div class="form__field">
							<div class="form__label"></div>
							<div class="form__inner">
								<input class="btn btn-primary" type="submit" value="{tlang('Save changes')}">
							</div>
						</div>

						{form_csrf()}
					</form>
				</div>
				<div class="col-sm-5"></div>
			</div>
		</div>

	</div><!-- /.content__container -->
</div><!-- /.content -->