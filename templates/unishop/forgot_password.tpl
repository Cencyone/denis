<div class="content">
	<div class="content__container">

		<div class="content__header">
			<h1 class="content__title">
					{tlang('Request new password')}
			</h1>
		</div>
		<div class="content__row">
			<div class="row">
				<div class="col-sm-7">
					<form class="form" action="{site_url('auth/forgot_password')}" method="post">
						
						<!-- Messages -->
						{if $success}
						<div class="form__messages">
							<p class="message message--success">{$success}</p>
						</div>
						{/if}
						
						{if $errors}
						<div class="form__messages">
							<p class="message message--error">{$errors}</p>
						</div>
						{/if}
						
						{if validation_errors()}
						<div class="form__messages">
							<div class="message message--error">
								<ul class="message__list">
									{validation_errors('<li class="message__item">', '</li>')}
								</ul>
							</div>
						</div>
						{/if}

						<!-- User Email field -->
						{view('includes/forms/input-base.tpl', [
							'label' => tlang('E-mail'),
							'type' => 'email',
							'name' => 'email',
							'value' => get_value('email'),
							'required' => true,
							'desc' => tlang('Please enter the e-mail associated with your account.')
						])}

						<!-- Captcha field -->      
						{if $cap_image}
							{view('includes/forms/input-captcha.tpl', [
								'label' => tlang('Security code'),
								'type' => 'text',
								'name' => 'captcha',
								'value' => get_value('captcha'),
								'required' => true,
								'desc' => tlang('Type the characters you see in this image.')
							])}
						{/if}

						<!-- Submit button -->
						<div class="form__field">
							<div class="form__label"></div>
							<div class="form__inner">
								<input class="btn btn-primary" type="submit" value="{tlang('Send request')}">
							</div>
						</div>

						<!-- Additional info -->
						<div class="form__field">
							<div class="form__label"></div>
							<div class="form__inner">
								<div class="form__desc">{tlang('Remembered your password?')}<br><a class="form__link" href="{site_url('auth/register')}">{tlang('Create Account')}</a> / <a class="form__link" href="{site_url('auth')}">{tlang('Sign in')}</a></div>
							</div>
						</div>

						{form_csrf()}
					</form>
				</div>
			</div>
		</div>

	</div><!-- /.content__container -->
</div><!-- /.content -->