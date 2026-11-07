/* Native block editor controls. No build step or frontend library. */
(function (blocks, element, editor, components, i18n, SSR) {
 const el = element.createElement, __ = i18n.__;
 const preview = (name, attributes) => el(SSR.default || SSR, {block: name, attributes: attributes});
 blocks.registerBlockType('avela/catalog', {
  apiVersion: 3, title: __('Avela product collection', 'avela'), icon: 'store', category: 'widgets',
  description: __('Products from WooCommerce. Edit products in Products; remove this block to hide it.', 'avela'),
  attributes: {mode: {type: 'string', default: 'featured'}, count: {type: 'number', default: 4}},
  supports: {html: false, className: false},
  edit: function (props) {
   return el('div', editor.useBlockProps(),
    el(editor.InspectorControls, {}, el(components.PanelBody, {title: __('Collection settings', 'avela')},
     el(components.SelectControl, {label: __('Collection', 'avela'), value: props.attributes.mode, options: [{label: __('Featured products', 'avela'), value: 'featured'}, {label: __('New arrivals', 'avela'), value: 'new'}, {label: __('Sale products', 'avela'), value: 'sale'}], onChange: value => props.setAttributes({mode: value})}),
     el(components.RangeControl, {label: __('Number of products', 'avela'), value: props.attributes.count, min: 1, max: 12, onChange: value => props.setAttributes({count: value})}))),
    preview('avela/catalog', props.attributes));
  }, save: () => null
 });
 [
  ['categories', __('Avela shop categories', 'avela'), 'category'],
  ['contact', __('Avela contact details', 'avela'), 'email'],
  ['store-page', __('Avela store page', 'avela'), 'cart']
 ].forEach(([name, title, icon]) => blocks.registerBlockType('avela/' + name, {
  apiVersion: 3, title, icon, category: 'widgets', supports: {html: false, className: name === 'categories' ? false : true},
  attributes: name === 'store-page' ? {kind: {type: 'string', default: 'tracking'}} : {},
  edit: props => el('div', editor.useBlockProps(),
   name === 'store-page' ? el(editor.InspectorControls, {}, el(components.PanelBody, {title: __('Page function', 'avela')}, el(components.SelectControl, {label: __('WooCommerce page', 'avela'), value: props.attributes.kind, options: ['tracking', 'cart', 'checkout', 'account'].map(v => ({label: v, value: v})), onChange: value => props.setAttributes({kind: value})}))) : null,
   name === 'contact' ? el('p', {}, __('Edit shared contact details in Appearance → Customize → Avela store details & style.', 'avela')) : null,
   preview('avela/' + name, props.attributes)), save: () => null
 }));
})(window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.components, window.wp.i18n, window.wp.serverSideRender);
