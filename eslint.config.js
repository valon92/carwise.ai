import pluginVue from 'eslint-plugin-vue'

export default [
  {
    ignores: ['node_modules/**', 'public/build/**', 'vendor/**', 'carwise-cpanel-upload-*/**'],
  },
  ...pluginVue.configs['flat/essential'],
  {
    files: ['resources/js/**/*.{js,vue}'],
    languageOptions: {
      ecmaVersion: 'latest',
      sourceType: 'module',
    },
    rules: {
      'vue/multi-word-component-names': 'off',
      'vue/no-reserved-component-names': 'off',
      'vue/no-parsing-error': 'off',
      'vue/no-unused-vars': 'off',
      'no-unused-vars': 'off',
      'no-undef': 'off',
    },
  },
]
