describe('Dépôt de fichier (US01)', () => {
  it('téléverse un fichier et affiche le lien généré', () => {
    const api = Cypress.env('apiUrl')
    const email = `up_${Date.now()}@example.com`

    cy.request('POST', `${api}/auth/register`, { email, password: 'motdepasse123' }).then((res) => {
      const token = res.body.access_token
      cy.visit('/upload', {
        onBeforeLoad: (win) => win.localStorage.setItem('token', token),
      })
    })

    cy.contains('Ajouter un fichier')
    cy.get('input[type=file]').selectFile('cypress/fixtures/example.txt')
    cy.contains('button', 'Télécharger').click()

    cy.contains('Copier le lien')
    cy.get('input[readonly]').invoke('val').should('match', /\/api\/v1\/download\//)
  })
})
