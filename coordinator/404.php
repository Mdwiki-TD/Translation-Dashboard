
<!-- 
    
\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*\s*=\s*(['"])\s*<[\s\S]*?>\s*\1;

(\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*\s*=\s*)(['"])(<\s*[\s\S]*?>\s*)\2;
$1<<<HTML\n$3\nHTML;

(\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*\s*=\s*)(['"])(\s*<[\s\S]*?>\s*)\2;

--------
(\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*|print|echo)(\s*\.*=\s*)(['"])(\s*)(<[\s\S]*?>\s*)\3;

$1$2<<<HTML$4$5$4HTML;
-->
<div class="content-wrapper pt-5">
    <section class="content">
      <div class="error-page">
        <h3 class="headline text-warning">404</h3>
        <div class="error-content">
          <h4><i class="fas fa-exclamation-triangle text-warning"></i> Oops! Page not found.</h4>
        </div>
      </div>
    </section>
  </div>
