<div class="content">
  <div class="content__container">

    <!-- Post title -->
    <div class="content__header">
      <h1 class="content__title">
        {$page.title}
      </h1>
    </div>

      <!-- Post description -->
      <div class="content__row">
        <div class="typo">{$page.full_text}</div>
      </div>

      <!-- Comments -->
      {if $page.comments_status == 1}
        <div class="content__row">
          <section class="frame-content">
            <div class="frame-content__header">
              <h2 class="frame-content__title">{tlang('Comments')}</h2>
            </div>
            <div class="frame-content__inner"
                 data-comments>
              {tpl_load_comments()}
            </div>
          </section>
        </div>
      {/if}

    </div><!-- /.content__container -->
  </div><!-- /.content -->