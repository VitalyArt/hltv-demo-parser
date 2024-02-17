module.exports = {
  pagePatterns: ['*.md'],
  base: 'hltv-demo-parser',
  title: 'HTLV Demo Parser',
  description: 'This package is designed to obtain information from the demo of the servers or games on the Half-Life 1 engine',
  themeConfig: {
    displayAllHeaders: true, // Default: false
    navbar: true,
    nav: [
      { text: 'Home', link: '/' },
      { text: 'GitHub', link: 'https://github.com/VitalyArt/hltv-demo-parser' },
    ],
    sidebar: [
      {
        title: 'Install',     // required
        path: 'install.html', // optional, link of the title, which should be an absolute path and must exist
        collapsable: false,   // optional, defaults to true
        sidebarDepth: 1,      // optional, defaults to 1
      },
      {
        title: 'Example',
        path: 'example.html', // optional, link of the title, which should be an absolute path and must exist
        collapsable: false,   // optional, defaults to true
        sidebarDepth: 1,      // optional, defaults to 1
      },
      {
        title: 'Reference',
        path: 'reference.html', // optional, link of the title, which should be an absolute path and must exist
        collapsable: false,     // optional, defaults to true
        sidebarDepth: 1,        // optional, defaults to 1
      },
    ]
  },
  home: true,
  actionText: 'Get Started →'
}
