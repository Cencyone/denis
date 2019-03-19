<div class="d_n">
    <b data-var>{literal}{{category.id}}{/literal}</b> - {lang('Category ID','smart_filter')}<br/>
    <b data-var>{literal}{{category.name}}{/literal}</b> - {lang('Category name','smart_filter')}<br/>
    <b data-var>{literal}{{category.description}}{/literal}</b> - {lang('Category description','smart_filter')}<br/>
    <b data-var>{literal}{{minPrice}}{/literal}</b>
    - {lang("Minimal price in category",'smart_filter')}<br/>
    <b data-var>{literal}{{maxPrice}}{/literal}</b>
    - {lang("Maximal price in category",'smart_filter')}<br/>
    <b data-var>{literal}{{property.name}}{/literal}</b> - {lang("Property name",'smart_filter')}<br/>
    <b data-var>{literal}{{value.name}}{/literal}</b> - {lang("Property value",'smart_filter')}
    <br/>
    <b data-var>{literal}{{brand.name}}{/literal}</b> - {lang("Brand name",'smart_filter')}<br/>

    <br/>
    {lang('Additional params for using with category name','smart_filter')}
    :<br/>
    <b>|translit</b> - {lang('Translit','smart_filter')}<br/>
    <b>|morphy(1..6)</b> - {lang('Number case of word','smart_filter')}<br/>
    {lang('Example','smart_filter')}:<br/>
    <b data-var>{literal}{{category.name|morphy(1)}} {/literal}</b> - {lang('Именительный', 'smart_filter')}<br/>
    <b data-var>{literal}{{category.name|morphy(2)}}{/literal}</b> - {lang('Родительный', 'smart_filter')}<br/>
    <b data-var>{literal}{{category.name|morphy(3)}}{/literal}</b> - {lang('Дательный', 'smart_filter')}<br/>
    <b data-var>{literal}{{category.name|morphy(4)}}{/literal}</b> - {lang('Винительный', 'smart_filter')}<br/>
    <b data-var>{literal}{{category.name|morphy(5)}}{/literal}</b> - {lang('Творительный', 'smart_filter')}<br/>
    <b data-var>{literal}{{category.name|morphy(6)}}{/literal}</b> - {lang('Предложный', 'smart_filter')}<br/>
    <b data-var>{literal}{{category.name|morphy(1..6)|translit}}{/literal}</b>
    - {lang('joint use', 'smart_filter')} <br/>
</div>
