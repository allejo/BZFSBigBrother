module.exports = {
  useTabs: false,
  singleQuote: true,
  trailingComma: 'all',
  bracketSameLine: false,
  semi: true,
  importOrder: [
    '^(?!\\.+\\/)[^.]+(?!\\.\\w+)$', // Anything that doesn't start with `./` or ../` and doesn't have an extension
    '^[./]+[^.]+$', // Anything that starts with `./` and doesn't have any periods
    '^(?!\\.+\\/)[^.]+\\.[a-z]+$', // Anything that doesn't start with `./` or `../` and has an extension
    '\\.[a-z]+$', // Anything that has an extension
  ],
  importOrderSeparation: true,
  twigFollowOfficialCodingStandards: false,
  twigMelodyPlugins: ['node_modules/prettier-plugin-twig-enhancements'],
};
