import { defineConfig } from 'vitepress'

export default defineConfig({
   title: 'Laravel Patches',
   description:
      'A simple, command-based patching system for Laravel — hidden, trackable, and disposable one-off commands for data migrations, one-time fixes, and complex deployments.',
   lang: 'en-US',
   cleanUrls: true,
   lastUpdated: true,

   head: [
      ['link', { rel: 'icon', type: 'image/webp', href: '/logo.webp' }],
      ['meta', { name: 'theme-color', content: '#e84e2a' }],
   ],

   themeConfig: {
      logo: '/logo.webp',
      siteTitle: 'Laravel Patches',

      nav: [
         { text: 'Guide', link: '/guide/introduction' },
         { text: 'Configuration', link: '/guide/configuration' },
         {
            text: 'Packagist',
            link: 'https://packagist.org/packages/aaix/laravel-patches',
         },
      ],

      sidebar: {
         '/guide/': [
            {
               text: 'Getting Started',
               items: [
                  { text: 'Introduction', link: '/guide/introduction' },
                  { text: 'Installation', link: '/guide/installation' },
               ],
            },
            {
               text: 'Usage',
               items: [
                  { text: 'Creating a Patch', link: '/guide/creating-a-patch' },
                  { text: 'Running Patches', link: '/guide/running-patches' },
                  { text: 'Patch Status', link: '/guide/patch-status' },
                  { text: 'Syncing Existing Patches', link: '/guide/syncing' },
               ],
            },
            {
               text: 'Reference',
               items: [{ text: 'Configuration', link: '/guide/configuration' }],
            },
         ],
      },

      socialLinks: [
         { icon: 'github', link: 'https://github.com/jonaaix/laravel-patches' },
      ],

      search: {
         provider: 'local',
      },

      editLink: {
         pattern:
            'https://github.com/jonaaix/laravel-patches/edit/main/docs/:path',
         text: 'Edit this page on GitHub',
      },

      footer: {
         message: 'Released under the MIT License.',
         copyright: 'Copyright © Laravel Patches contributors',
      },
   },
})
