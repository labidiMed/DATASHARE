describe('Authentification (US03/US04)', () => {
  it('inscription, déconnexion puis reconnexion', () => {
    const email = `auth_${Date.now()}@example.com`
    const password = 'motdepasse123'

    cy.visit('/register')
    cy.get('#email').type(email)
    cy.get('#password').type(password)
    cy.get('#confirm').type(password)
    cy.contains('button', 'Créer un compte').click()

    cy.contains('Mes fichiers')

    cy.contains('Déconnexion').click()
    cy.contains('button', 'Connexion')

    cy.visit('/login')
    cy.get('#email').type(email)
    cy.get('#password').type(password)
    cy.contains('button', 'Connexion').click()

    cy.contains('Mes fichiers')
  })
})
