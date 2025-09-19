module.exports = {
  plugins: [
    require('postcss-prefix-selector')({
      prefix: '.bs5',
      transform: function (prefix, selector, prefixedSelector) {
        // Prevent prefixing keyframes or global rules
        if (selector.startsWith('html') || selector.startsWith('body')) {
          return selector.replace(/^(html|body)/, prefix);
        }
        if (selector.startsWith('@') || selector.startsWith(':root')) {
          return selector;
        }
        return prefixedSelector;
      }
    })
  ]
};

