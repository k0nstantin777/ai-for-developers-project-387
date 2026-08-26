module.exports = {
  ci: {
    collect: {
      url: [
        'http://localhost:5173/',
      ],
      numberOfRuns: 1,
      settings: {
        preset: 'desktop',
        output: 'html',
      },
    },
    upload: {
      target: 'temporary-public-storage',
    },
    assert: {
      assertions: {
        'categories:performance': ['warn', { minScore: 0.5 }],
        'categories:accessibility': ['warn', { minScore: 0.7 }],
        'categories:best-practices': ['warn', { minScore: 0.7 }],
        'categories:seo': ['warn', { minScore: 0.7 }],
      },
      includePassedAssertions: true,
    },
  },
};
