describe('Téléchargement public (US02)', () => {
  it('affiche les métadonnées puis télécharge un fichier public', () => {
    const api = Cypress.env('apiUrl')
    const email = `dl_${Date.now()}@example.com`

    // Inscription rapide via l'API, puis upload réel via l'interface
    cy.request('POST', `${api}/auth/register`, { email, password: 'motdepasse123' }).then((res) => {
      cy.visit('/upload', {
        onBeforeLoad: (win) => win.localStorage.setItem('token', res.body.access_token),
      })
    })

    cy.get('input[type=file]').selectFile('cypress/fixtures/example.txt')
    cy.contains('button', 'Télécharger').click()
    cy.contains('Copier le lien')

    // On récupère le lien généré et on visite la page publique de téléchargement
    cy.get('input[readonly]').invoke('val').then((url) => {
      const token = url.split('/download/')[1]
      cy.visit(`/download/${token}`)
      cy.contains('Télécharger un fichier')
      cy.contains('example.txt')
      cy.contains('button', 'Télécharger').click()
    })
  })

  it('affiche une erreur pour un lien invalide', () => {
    cy.visit('/download/lien-invalide-xyz')
    cy.contains('invalide')
  })
})
