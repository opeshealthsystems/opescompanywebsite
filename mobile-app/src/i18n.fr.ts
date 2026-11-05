// French translations keyed by the English source string. Keep keys identical to the t('…') text.
// Entries marked "// dynamic" are reached through t(variable) (catalog titles, labels, API status codes).
// Run `npm run i18n:check` after changing any user-facing text.
export const fr: Record<string, string> = {
  // Brand
  'OPES': "OPES",
  'HEALTH': "HEALTH",
  'SYSTEMS': "SYSTEMS",
  'OPES Health': "OPES Health",
  'OPES Health Systems': "OPES Health Systems",
  'OPES Health mobile': "OPES Health mobile",

  // Workspaces and features — dynamic
  'Staff & administration': "Personnel et administration", // dynamic
  'Customer workspace': "Espace client", // dynamic
  'Practitioner workspace': "Espace praticien", // dynamic
  'Testing workspace': "Espace de test", // dynamic
  'Partner workspace': "Espace partenaire", // dynamic
  'Quotes': "Devis", // dynamic
  'Review, prepare and update customer quotations.': "Consultez, préparez et mettez à jour les devis clients.", // dynamic
  'Customers': "Clients", // dynamic
  'Review customer facilities and create customer profiles.': "Consultez les établissements clients et créez des profils clients.", // dynamic
  'Contacts & leads': "Contacts et prospects", // dynamic
  'Add contacts, review enquiries and update follow-up status.': "Ajoutez des contacts, consultez les demandes et mettez à jour le suivi.", // dynamic
  'Partner applications': "Candidatures partenaires", // dynamic
  'Review incoming partnership applications.': "Examinez les candidatures de partenariat reçues.", // dynamic
  'CRM deals': "Opportunités CRM", // dynamic
  'Track business opportunities and deal value.': "Suivez les opportunités commerciales et leur valeur.", // dynamic
  'Service requests': "Demandes de service", // dynamic
  'Request and track services for your organization.': "Demandez et suivez des services pour votre organisation.", // dynamic
  'Support tickets': "Tickets d’assistance", // dynamic
  'Open a support request and follow replies.': "Ouvrez une demande d’assistance et suivez les réponses.", // dynamic
  'Invoices': "Factures", // dynamic
  'Review invoices issued to your organization.': "Consultez les factures émises pour votre organisation.", // dynamic
  'Account profile': "Profil du compte", // dynamic
  'Maintain your organization and contact details.': "Tenez à jour les coordonnées de votre organisation.", // dynamic
  'Professional profile': "Profil professionnel", // dynamic
  'Maintain your practitioner profile and workplace details.': "Tenez à jour votre profil de praticien et votre lieu d’exercice.", // dynamic
  'Courses': "Cours", // dynamic
  'Browse courses, study lessons, save progress and view certificates.': "Parcourez les cours, étudiez les leçons, enregistrez votre progression et consultez vos certificats.", // dynamic
  'Certificates': "Certificats", // dynamic
  'View certificates associated with your account.': "Consultez les certificats associés à votre compte.", // dynamic
  'Assignments': "Missions de test", // dynamic
  'Update testing progress and report bugs.': "Mettez à jour l’avancement des tests et signalez des bogues.", // dynamic
  'Bug reports': "Rapports de bogues", // dynamic
  'Review issues reported from your assigned product tests.': "Consultez les problèmes signalés lors de vos tests.", // dynamic
  'Partner directory': "Annuaire des partenaires", // dynamic
  'Browse organizations in the OPES partner network.': "Parcourez les organisations du réseau de partenaires OPES.", // dynamic
  'Apply to partner': "Devenir partenaire", // dynamic
  'Send a partnership application to OPES Health Systems.': "Envoyez une candidature de partenariat à OPES Health Systems.", // dynamic
  'My organizations': "Mes organisations", // dynamic
  'View approved organizations explicitly linked to your partner account and maintain owner-managed details.': "Consultez les organisations approuvées liées à votre compte partenaire et gérez les informations réservées au propriétaire.", // dynamic
  'Notifications': "Notifications",
  'Review account updates and mark alerts as read.': "Consultez les mises à jour du compte et marquez les alertes comme lues.", // dynamic

  // Tabs, overview and demo labels — dynamic
  'Contacts': "Contacts", // dynamic
  'Services': "Services", // dynamic
  'Support': "Assistance", // dynamic
  'Organizations': "Organisations", // dynamic
  'Apply': "Postuler", // dynamic
  'Leads': "Prospects", // dynamic
  'Staff member': "Membre du personnel", // dynamic
  'Customer': "Client", // dynamic
  'Health practitioner': "Professionnel de santé", // dynamic
  'Tester': "Testeur", // dynamic
  'Partner': "Partenaire", // dynamic
  'Doctor / Physician': "Médecin", // dynamic
  'Nurse': "Infirmier / Infirmière", // dynamic
  'Radiologist': "Radiologue", // dynamic
  'Cardiologist': "Cardiologue", // dynamic
  'Pharmacist': "Pharmacien", // dynamic
  'Laboratory Technician': "Technicien de laboratoire", // dynamic
  'Health Administrator': "Administrateur de santé", // dynamic
  'Researcher': "Chercheur", // dynamic

  // Form titles — dynamic
  'Prepare a quote': "Préparer un devis", // dynamic
  'Create customer facility profile': "Créer le profil d’établissement client", // dynamic
  'Add a contact or enquiry': "Ajouter un contact ou une demande", // dynamic
  'Add CRM deal': "Ajouter une opportunité CRM", // dynamic
  'Request a service': "Demander un service",
  'Open support ticket': "Ouvrir un ticket d’assistance", // dynamic
  'Edit account profile': "Modifier le profil du compte", // dynamic
  'Edit practitioner profile': "Modifier le profil de praticien", // dynamic
  'Report a bug': "Signaler un bogue", // dynamic
  'Reply to support': "Répondre à l’assistance", // dynamic
  'Partnership application': "Candidature de partenariat", // dynamic
  'Edit organization profile': "Modifier le profil de l’organisation",
  'Complete action': "Terminer l’action", // dynamic

  // Readable API codes and field names — dynamic (humanize)
  'Account ID': "ID du compte", // dynamic
  'Professional bio': "Biographie professionnelle",
  'Closed – won': "Clôturée – gagnée", // dynamic
  'Closed – lost': "Clôturée – perdue", // dynamic
  'Specialist centre': "Centre spécialisé", // dynamic
  'Bug report': "Rapport de bogue", // dynamic
  'Years of experience': "Années d’expérience",
  'Workplace': "Lieu d’exercice",
  'Facility': "Établissement",
  'Facility type': "Type d’établissement",
  'Created': "Créé", // dynamic
  'Last updated': "Dernière mise à jour", // dynamic
  'Issued': "Délivré", // dynamic
  'Due date': "Échéance", // dynamic
  'Valid until': "Valable jusqu’au", // dynamic
  'Preferred date': "Date souhaitée",
  'NGO': "ONG", // dynamic
  'NGOs': "ONG", // dynamic
  'ID': "ID", // dynamic
  'EMR': "DPI", // dynamic
  'CRM': "CRM", // dynamic
  'URL': "URL", // dynamic
  'Installation': "Installation", // dynamic
  'Maintenance': "Maintenance", // dynamic
  'Training': "Formation", // dynamic
  'Other': "Autre", // dynamic
  'Clinic': "Clinique", // dynamic
  'Hospital': "Hôpital", // dynamic
  'Laboratory': "Laboratoire", // dynamic
  'Pharmacy': "Pharmacie", // dynamic
  'Government': "Administration publique", // dynamic
  'Ngo': "ONG", // dynamic
  'Qualification': "Qualification", // dynamic
  'Discovery': "Découverte", // dynamic
  'Proposal': "Proposition", // dynamic
  'Negotiation': "Négociation", // dynamic
  'Billing': "Facturation", // dynamic
  'Technical': "Technique", // dynamic
  'Low': "Faible", // dynamic
  'Medium': "Moyenne", // dynamic
  'High': "Élevée", // dynamic
  'Urgent': "Urgente", // dynamic
  'Normal': "Normale", // dynamic
  'Referral': "Recommandation", // dynamic
  'Technology': "Technologie", // dynamic
  'Distribution': "Distribution", // dynamic
  'Implementation': "Mise en œuvre", // dynamic
  'Draft': "Brouillon", // dynamic
  'Sent': "Envoyé", // dynamic
  'Accepted': "Accepté", // dynamic
  'Declined': "Refusé", // dynamic
  'Rejected': "Rejeté", // dynamic
  'Approved': "Approuvé", // dynamic
  'Pending': "En attente", // dynamic
  'Reviewing': "En cours d’examen", // dynamic
  'Open': "Ouvert", // dynamic
  'Closed': "Clôturé", // dynamic
  'New': "Nouveau", // dynamic
  'Contacted': "Contacté", // dynamic
  'Qualified': "Qualifié", // dynamic
  'In progress': "En cours",
  'Completed': "Terminé",
  'Enrolled': "Inscrit", // dynamic
  'Available': "Disponible",
  'Scheduled': "Planifié", // dynamic
  'Submitted': "Soumis", // dynamic
  'Due': "À payer", // dynamic
  'Paid': "Payée", // dynamic
  'Overdue': "En retard", // dynamic
  'Cancelled': "Annulé", // dynamic
  'Triaged': "Trié", // dynamic
  'Resolved': "Résolu", // dynamic
  'Active': "Actif", // dynamic
  'Inactive': "Inactif", // dynamic
  'Expired': "Expiré", // dynamic
  'Owner': "Propriétaire",
  'Member': "Membre",
  'Staff': "Personnel", // dynamic
  'Practitioner': "Praticien",
  'Admin': "Administrateur", // dynamic
  'Super admin': "Super administrateur", // dynamic
  'Name': "Nom", // dynamic
  'Email': "E-mail",
  'Phone': "Téléphone",
  'Country': "Pays",
  'City': "Ville",
  'Address': "Adresse",
  'Description': "Description", // dynamic
  'Subject': "Objet",
  'Priority': "Priorité",
  'Status': "Statut", // dynamic
  'Title': "Titre", // dynamic
  'Reference': "Référence", // dynamic
  'Total': "Total", // dynamic
  'Subtotal': "Sous-total", // dynamic
  'Currency': "Devise",
  'Quantity': "Quantité",
  'Unit price': "Prix unitaire",
  'Value': "Valeur",
  'Probability': "Probabilité", // dynamic
  'Stage': "Étape", // dynamic
  'Notes': "Notes",
  'Message': "Message", // dynamic
  'Products': "Produits", // dynamic
  'Source': "Source", // dynamic
  'Specialty': "Spécialité",
  'Organization name': "Nom de l’organisation",
  'Contact name': "Nom du contact",
  'Partner type': "Type de partenariat", // dynamic
  'Website': "Site web",
  'Quote id': "ID du devis", // dynamic
  'Tax rate': "Taux de taxe", // dynamic
  'Tax amount': "Montant de la taxe", // dynamic
  'Facility name': "Nom de l’établissement",
  'Workplace city': "Ville du lieu d’exercice",
  'Workplace country': "Pays du lieu d’exercice",
  'Product slug': "Identifiant produit", // dynamic
  'Preferred time': "Heure souhaitée",
  'Location': "Lieu",
  'Type': "Type",
  'Organization type': "Type d’organisation",
  'Target market': "Marché cible",
  'Membership role': "Rôle dans l’organisation", // dynamic
  'Certificate number': "Numéro de certificat", // dynamic
  'Course id': "ID du cours", // dynamic
  'Product': "Produit", // dynamic
  'Value amount': "Montant", // dynamic
  'Expected close date': "Date de clôture prévue",

  // Screens
  ' Reference: {id}': " Référence : {id}",
  ' · {channel} channel': " · canal {channel}",
  ' · {count} minutes': " · {count} minutes",
  '1 unread notification': "1 notification non lue",
  'Account': "Compte",
  'Account & profile': "Compte et profil",
  'Account access and write actions follow your live API permissions.': "L’accès et les actions d’écriture suivent vos autorisations API actuelles.",
  'Account action': "Action du compte",
  'Account security': "Sécurité du compte",
  'Account type': "Type de compte",
  'Account update': "Mise à jour du compte",
  'Account updates': "Mises à jour du compte",
  'Account updates and service messages will appear here.': "Les mises à jour du compte et les messages de service apparaîtront ici.",
  'Add a contact': "Ajouter un contact",
  'Add partner account': "Ajouter un compte partenaire",
  'All courses': "Tous les cours",
  'All notifications marked as read.': "Toutes les notifications ont été marquées comme lues.",
  'An OPES administrator must approve the partnership and link your account to the organization. Contact your OPES relationship manager if you believe this is an error.': "Un administrateur OPES doit approuver le partenariat et lier votre compte à l’organisation. Contactez votre chargé de relation OPES si vous pensez qu’il s’agit d’une erreur.",
  'Answer every question before submitting.': "Répondez à toutes les questions avant de soumettre.",
  'Answer review': "Revue des réponses",
  'App & security': "Application et sécurité",
  'Application information': "Informations sur la candidature",
  'Approve': "Approuver",
  'Approve partner application?': "Approuver la candidature partenaire ?",
  'Ask your administrator for practitioner:write access to enroll in courses.': "Demandez à votre administrateur l’accès practitioner:write pour vous inscrire aux cours.",
  'Ask your administrator for practitioner:write access to save lesson progress.': "Demandez à votre administrateur l’accès practitioner:write pour enregistrer votre progression.",
  'Ask your course administrator to add the lesson content.': "Demandez à l’administrateur du cours d’ajouter le contenu de la leçon.",
  'Assessment details': "Détails de l’évaluation",
  'Assessment in progress': "Évaluation en cours",
  'Assessment passed': "Évaluation réussie",
  'Assessment result': "Résultat de l’évaluation",
  'Assignment status updated.': "Statut de la mission mis à jour.",
  'At a glance': "En un coup d’œil",
  'Attempt in progress': "Tentative en cours",
  'Back to course': "Retour au cours",
  'Back to courses': "Retour aux cours",
  'Back to dashboard': "Retour au tableau de bord",
  'Back to learning': "Retour à l’apprentissage",
  'Back to sign in': "Retour à la connexion",
  'Back to workspace': "Retour à l’espace de travail",
  'Back to your dashboard': "Retour à votre tableau de bord",
  'Begin exam': "Commencer l’examen",
  'Browse courses': "Parcourir les cours",
  'Browse partner directory': "Parcourir l’annuaire des partenaires",
  'Bug title': "Titre du bogue",
  'Build a partnership with OPES': "Construire un partenariat avec OPES",
  'COMPLETED': "TERMINÉ",
  'COURSE COMPLETED': "COURS TERMINÉ",
  'Cancel': "Annuler",
  'Certificates could not be loaded.': "Impossible de charger les certificats.",
  'Check for app updates': "Rechercher des mises à jour",
  'Checking for updates…': "Recherche de mises à jour…",
  'Choose All courses or refresh to check the course library.': "Choisissez Tous les cours ou actualisez pour consulter la bibliothèque.",
  'Choose a date': "Choisir une date",
  'Choose a new password': "Choisissez un nouveau mot de passe",
  'Choose a password with at least 8 characters.': "Choisissez un mot de passe d’au moins 8 caractères.",
  'Choose a service type, describe what you need and select a preferred date.': "Choisissez un type de service, décrivez votre besoin et sélectionnez une date souhaitée.",
  'Choose the best answer for each question. Your progress saves as you go.': "Choisissez la meilleure réponse à chaque question. Votre progression est enregistrée au fur et à mesure.",
  'City (optional)': "Ville (facultatif)",
  'Clear date': "Effacer la date",
  'Clinic, hospital, laboratory…': "Clinique, hôpital, laboratoire…",
  'Close contact': "Clôturer le contact",
  'Complete all study lessons in an eligible course. Your certificate will appear here when it is issued.': "Terminez toutes les leçons d’un cours éligible. Votre certificat apparaîtra ici dès sa délivrance.",
  'Complete the organization, contact, email, country and partnership summary fields.': "Renseignez l’organisation, le contact, l’e-mail, le pays et le résumé du partenariat.",
  'Completing secure sign-in…': "Finalisation de la connexion sécurisée…",
  'Confirm new password': "Confirmer le nouveau mot de passe",
  'Confirm password': "Confirmer le mot de passe",
  'Contact marked {status}.': "Contact marqué : {status}.",
  'Continue learning': "Poursuivre l’apprentissage",
  'Continue studying': "Continuer à étudier",
  'Correct': "Correct",
  'Could not add this partner account.': "Impossible d’ajouter ce compte partenaire.",
  'Could not check for updates. Try again later.': "Impossible de rechercher des mises à jour. Réessayez plus tard.",
  'Could not check for updates: {reason}': "Impossible de rechercher des mises à jour : {reason}",
  'Could not load organization members.': "Impossible de charger les membres de l’organisation.",
  'Could not load profile details.': "Impossible de charger le profil.",
  'Could not load the current profile.': "Impossible de charger le profil actuel.",
  'Could not load the organization profile.': "Impossible de charger le profil de l’organisation.",
  'Could not load these records.': "Impossible de charger ces enregistrements.",
  'Could not open secure sign-in.': "Impossible d’ouvrir la connexion sécurisée.",
  'Could not remove this member.': "Impossible de retirer ce membre.",
  'Could not save your changes.': "Impossible d’enregistrer vos modifications.",
  'Country and organization description are required.': "Le pays et la description de l’organisation sont obligatoires.",
  'Course': "Cours",
  'Course assessment': "Évaluation du cours",
  'Course certificate': "Certificat de cours",
  'Course certificates': "Certificats de cours",
  'Course completed': "Cours terminé",
  'Course completion certificate': "Certificat de fin de cours",
  'Course details could not be loaded.': "Impossible de charger les détails du cours.",
  'Course enrollment saved.': "Inscription au cours enregistrée.",
  'Course library': "Bibliothèque de cours",
  'Course progress': "Progression du cours",
  'Courses & study': "Cours et études",
  'Courses could not be loaded.': "Impossible de charger les cours.",
  'Create a quote': "Créer un devis",
  'Create account': "Créer le compte",
  'Create an OPES account': "Créer un compte OPES",
  'Create an account': "Créer un compte",
  'Create your account in the app. Your account type and permissions are assigned by OPES based on this registration.': "Créez votre compte dans l’application. Votre type de compte et vos autorisations sont attribués par OPES selon cette inscription.",
  'Create: {item}': "Créer : {item}",
  'Creating account…': "Création du compte…",
  'Customer / enquiry': "Client / demande",
  'Customer account ID': "ID du compte client",
  'DEMO ACCOUNT · Fictional data. Changes stay local to this sample workspace.': "COMPTE DÉMO · Données fictives. Les modifications restent dans cet espace d’exemple.",
  'DEMO MODE · All records and actions on this screen are fictional and local to this app.': "MODE DÉMO · Les données et actions de cet écran sont fictives et restent dans l’application.",
  'DEMO MODE · Completing this lesson changes only the sample account’s progress.': "MODE DÉMO · Terminer cette leçon ne modifie que la progression du compte d’exemple.",
  'DEMO MODE · Progress is sample data stored only in this app.': "MODE DÉMO · La progression est une donnée d’exemple stockée uniquement dans l’application.",
  'DEMO MODE · Sample certificate details are fictional and stay in this app.': "MODE DÉMO · Les certificats d’exemple sont fictifs et restent dans l’application.",
  'DEMO MODE · Sample notifications are fictional and remain on this device.': "MODE DÉMO · Les notifications d’exemple sont fictives et restent sur cet appareil.",
  'DEMO MODE · Study progress and course actions are saved locally in this sample workspace.': "MODE DÉMO · La progression et les actions de cours sont enregistrées localement dans cet espace d’exemple.",
  'DEMO MODE · Submitting here changes only fictional sample data on this device.': "MODE DÉMO · Cet envoi ne modifie que des données fictives sur cet appareil.",
  'Date not provided': "Date non renseignée",
  'Deal title': "Titre de l’opportunité",
  'Demo action': "Action de démonstration",
  'Demo action saved locally.': "Action de démonstration enregistrée localement.",
  'Demo course records do not include assessments. Live practitioner accounts can take exams published for their enrolled courses.': "Les cours de démonstration n’incluent pas d’évaluations. Les comptes praticiens réels peuvent passer les examens publiés pour leurs cours.",
  'Demo updated: {feature} is now {status}.': "Démo mise à jour : {feature} est maintenant « {status} ».",
  'Describe the issue': "Décrivez le problème",
  'Edit profile': "Modifier le profil",
  'Email address': "Adresse e-mail",
  'Email me a reset link': "M’envoyer un lien de réinitialisation",
  'Enroll and start learning': "S’inscrire et commencer",
  'Enroll in course': "S’inscrire au cours",
  'Enroll in this course to see its published exams.': "Inscrivez-vous à ce cours pour voir ses examens publiés.",
  'Enrollment could not be saved.': "Impossible d’enregistrer l’inscription.",
  'Enter a quote title, an item, a positive quantity and a valid price.': "Saisissez un titre de devis, un article, une quantité positive et un prix valide.",
  'Enter a valid customer account ID and facility name.': "Saisissez un ID de compte client valide et le nom de l’établissement.",
  'Enter a valid email address.': "Saisissez une adresse e-mail valide.",
  'Enter both a bug title and the steps or details.': "Saisissez le titre du bogue et les étapes ou détails.",
  'Enter both a subject and a description.': "Saisissez un objet et une description.",
  'Enter the account email and reset token from your email.': "Saisissez l’e-mail du compte et le code de réinitialisation reçu par e-mail.",
  'Enter the contact name and email address.': "Saisissez le nom et l’adresse e-mail du contact.",
  'Enter the deal title, value and a probability from 0 to 100.': "Saisissez le titre, la valeur et une probabilité de 0 à 100.",
  'Enter the email and reset token provided by OPES Health.': "Saisissez l’e-mail et le code de réinitialisation fournis par OPES Health.",
  'Enter your country.': "Saisissez votre pays.",
  'Enter your facility name, facility type and country.': "Saisissez le nom, le type et le pays de votre établissement.",
  'Enter your name and email address.': "Saisissez votre nom et votre adresse e-mail.",
  'Exams & assessments': "Examens et évaluations",
  'Exit': "Quitter",
  'Exit demo workspace': "Quitter l’espace de démonstration",
  'Exit demo workspace?': "Quitter l’espace de démonstration ?",
  'Expo Go cannot receive the OPES Health sign-in callback. Install the OPES Health app build to sign in to your account.': "Expo Go ne peut pas recevoir le retour de connexion OPES Health. Installez l’application OPES Health pour vous connecter.",
  'Fictional accounts for trying the app. Nothing is sent to OPES, and changes reset when you leave.': "Comptes fictifs pour essayer l’application. Rien n’est envoyé à OPES et les modifications sont effacées à la sortie.",
  'Full name': "Nom complet",
  'Have your organization details, country, contact email and a short partnership proposal ready.': "Préparez les informations de votre organisation, le pays, l’e-mail de contact et une courte proposition de partenariat.",
  'Health facility': "Établissement de santé",
  'Hide customer details': "Masquer les détails du client",
  'Hide details': "Masquer les détails",
  'Hide quote': "Masquer le devis",
  'Hide quote details': "Masquer les détails du devis",
  'Hide sample workspaces': "Masquer les espaces d’exemple",
  'Home': "Accueil",
  'How would you like to partner?': "Comment souhaitez-vous collaborer ?",
  'I already have a reset token': "J’ai déjà un code de réinitialisation",
  'IN PROGRESS': "EN COURS",
  'If this email is associated with an account, a password reset link has been sent. Follow the link from your email, then return here to sign in.': "Si cet e-mail est associé à un compte, un lien de réinitialisation a été envoyé. Suivez le lien reçu, puis revenez ici pour vous connecter.",
  'Individual': "Particulier",
  'Invite an existing, verified OPES partner account and choose its organization role.': "Invitez un compte partenaire OPES existant et vérifié, puis choisissez son rôle dans l’organisation.",
  'Item': "Article",
  'Item {number}': "Article {number}",
  'Join OPES Health': "Rejoindre OPES Health",
  'Just looking? Try a sample workspace': "Simple visite ? Essayez un espace d’exemple",
  'Keep studying': "Continuez à étudier",
  'Last score · {score}%': "Dernier score · {score} %",
  'Learning': "Apprentissage",
  'Learning check': "Contrôle des acquis",
  'Learning record': "Dossier de formation",
  'Lesson complete. Your course progress has been updated.': "Leçon terminée. Votre progression a été mise à jour.",
  'Lesson complete. Your sample progress is saved.': "Leçon terminée. Votre progression d’exemple est enregistrée.",
  'Lesson notes are not available yet': "Les notes de la leçon ne sont pas encore disponibles",
  'Lesson progress could not be saved.': "Impossible d’enregistrer la progression de la leçon.",
  'Lesson {number}': "Leçon {number}",
  'Lessons are being prepared': "Les leçons sont en préparation",
  'Live API actions for this feature have not been published yet.': "Les actions API pour cette fonctionnalité ne sont pas encore publiées.",
  'Manage account information and work items.': "Gérez les informations du compte et les éléments de travail.",
  'Manage members': "Gérer les membres",
  'Manage organization members': "Gérer les membres de l’organisation",
  'Mark all read': "Tout marquer comme lu",
  'Mark as read': "Marquer comme lu",
  'Mark as sent': "Marquer comme envoyé",
  'Mark assignment complete?': "Marquer la mission comme terminée ?",
  'Mark complete': "Marquer comme terminé",
  'Mark contacted': "Marquer comme contacté",
  'Mark lesson complete': "Marquer la leçon comme terminée",
  'New password (minimum 8 characters)': "Nouveau mot de passe (8 caractères minimum)",
  'New unread updates will appear here.': "Les nouvelles mises à jour non lues apparaîtront ici.",
  'Next lesson': "Leçon suivante",
  'Next month': "Mois suivant",
  'Next question': "Question suivante",
  'No answer': "Aucune réponse",
  'No approved organizations are linked yet': "Aucune organisation approuvée n’est encore liée",
  'No course certificates yet': "Aucun certificat de cours pour l’instant",
  'No courses in this view': "Aucun cours dans cette vue",
  'No exams are published for this course': "Aucun examen n’est publié pour ce cours",
  'No line item detail was included in this response.': "Aucun détail de ligne n’a été fourni.",
  'No members are linked to this organization.': "Aucun membre n’est lié à cette organisation.",
  'No mobile workspace for this account yet': "Pas encore d’espace mobile pour ce compte",
  'No notifications yet': "Aucune notification pour l’instant",
  'No professional validation certificate is linked to this account.': "Aucun certificat de validation professionnelle n’est lié à ce compte.",
  'No time limit': "Sans limite de temps",
  'Not passed': "Non réussi",
  'Not provided': "Non renseigné",
  'Not set': "Non défini",
  'Not signed in': "Non connecté",
  'Notes and enquiry': "Notes et demande",
  'Nothing here yet': "Rien pour l’instant",
  'Notification marked as read.': "Notification marquée comme lue.",
  'Notifications could not be loaded.': "Impossible de charger les notifications.",
  'Notifications could not be updated.': "Impossible de mettre à jour les notifications.",
  'Only active, email-verified accounts with the OPES partner role can be added. Assign another owner before removing or changing the last owner.': "Seuls les comptes actifs, à e-mail vérifié et dotés du rôle partenaire OPES peuvent être ajoutés. Désignez un autre propriétaire avant de retirer ou modifier le dernier.",
  'Open a support ticket': "Ouvrir un ticket d’assistance",
  'Open a ticket before replying.': "Ouvrez un ticket avant de répondre.",
  'Open lesson video': "Ouvrir la vidéo de la leçon",
  'Open messages': "Ouvrir les messages",
  'Open my account': "Ouvrir mon compte",
  'Open notifications': "Ouvrir les notifications",
  'Open the assignment you tested and use Report bug there, so the report is filed against the right test.': "Ouvrez la mission testée et utilisez Signaler un bogue depuis celle-ci, afin que le rapport soit associé au bon test.",
  'Open {name}': "Ouvrir {name}",
  'Opening exam…': "Ouverture de l’examen…",
  'Organization access': "Accès à l’organisation",
  'Organization description': "Description de l’organisation",
  'Organization member added.': "Membre ajouté à l’organisation.",
  'Organization members · {count}': "Membres de l’organisation · {count}",
  'Organization name, approval and partnership status are managed by OPES. This form edits contact and organization details only.': "Le nom, l’approbation et le statut du partenariat sont gérés par OPES. Ce formulaire modifie uniquement les coordonnées et informations de l’organisation.",
  'Organization profile editing is available to linked partner owners on a live account.': "La modification du profil est réservée aux propriétaires partenaires liés, sur un compte réel.",
  'Organization role': "Rôle dans l’organisation",
  'Organization type (optional)': "Type d’organisation (facultatif)",
  'Over-the-air updates are available in an installed OPES preview or store build.': "Les mises à jour à distance sont disponibles dans une version OPES installée (préversion ou boutique).",
  'PROFESSIONAL VALIDATION': "VALIDATION PROFESSIONNELLE",
  'Partner account': "Compte partenaire",
  'Partner account email': "E-mail du compte partenaire",
  'Partner application marked {status}.': "Candidature partenaire marquée : {status}.",
  'Partnership type': "Type de partenariat",
  'Pass mark {mark}%': "Seuil de réussite {mark} %",
  'Passed': "Réussi",
  'Passed · {score}%': "Réussi · {score} %",
  'Password (minimum 8 characters)': "Mot de passe (8 caractères minimum)",
  'Phone (optional)': "Téléphone (facultatif)",
  'Pipeline stage': "Étape du pipeline",
  'Please wait…': "Veuillez patienter…",
  'Practitioner accounts can access learning, study progress and certificates after you sign in. Public registration does not grant staff or administrator permissions.': "Les comptes praticiens accèdent à la formation, à la progression et aux certificats après connexion. L’inscription publique n’accorde aucune autorisation de personnel ou d’administrateur.",
  'Prepared by': "Préparé par",
  'Previous': "Précédent",
  'Previous lesson': "Leçon précédente",
  'Previous month': "Mois précédent",
  'Probability (%)': "Probabilité (%)",
  'Product or service': "Produit ou service",
  'Product slug (optional)': "Identifiant produit (facultatif)",
  'Products of interest': "Produits d’intérêt",
  'Profession': "Profession",
  'Professional validation': "Validation professionnelle",
  'Profile details': "Détails du profil",
  'Qualify contact': "Qualifier le contact",
  'Question {index} of {total} · {answered} answered': "Question {index} sur {total} · {answered} répondue(s)",
  'Quick actions': "Actions rapides",
  'Quote marked as sent.': "Devis marqué comme envoyé.",
  'Quote summary': "Résumé du devis",
  'Quote title': "Titre du devis",
  'ROLE AND ACCESS': "RÔLE ET ACCÈS",
  'Read or watch each lesson in order. Course assessments are separate and are shown only when provided for the course.': "Lisez ou regardez chaque leçon dans l’ordre. Les évaluations sont distinctes et n’apparaissent que si le cours en propose.",
  'Recently': "Récemment",
  'Record {number}': "Enregistrement {number}",
  'Records · {count}': "Enregistrements · {count}",
  'Refresh': "Actualiser",
  'Refresh notifications': "Actualiser les notifications",
  'Reject': "Rejeter",
  'Reject partner application?': "Rejeter la candidature partenaire ?",
  'Remove from organization': "Retirer de l’organisation",
  'Reply': "Réponse",
  'Reply to ticket': "Répondre au ticket",
  'Report a bug on an assignment': "Signaler un bogue sur une mission",
  'Report bug': "Signaler un bogue",
  'Request a reset link for your OPES account.': "Demandez un lien de réinitialisation pour votre compte OPES.",
  'Request another reset link': "Demander un autre lien",
  'Request failed ({status})': "La requête a échoué ({status})",
  'Reset password': "Réinitialiser le mot de passe",
  'Reset token': "Code de réinitialisation",
  'Reset your password': "Réinitialiser votre mot de passe",
  'Resume exam': "Reprendre l’examen",
  'Return to sign in': "Retour à la connexion",
  'Review course': "Revoir le cours",
  'Review course awards and professional validation certificates associated with your account.': "Consultez les certificats de cours et de validation professionnelle associés à votre compte.",
  'Review result': "Voir le résultat",
  'Review submitted result': "Voir le résultat soumis",
  'Review the notes and video, then mark the lesson complete. Any formal course assessment will be presented separately when the course provides one.': "Lisez les notes et regardez la vidéo, puis marquez la leçon comme terminée. Toute évaluation formelle sera présentée séparément si le cours en propose une.",
  'Review this answer': "Revoir cette réponse",
  'Sample changes will be reset.': "Les modifications d’exemple seront effacées.",
  'Sample workspace reference': "Référence de l’espace d’exemple",
  'Save and continue': "Enregistrer et continuer",
  'Saved sign-in expired. Sign in again. {reason}': "La connexion enregistrée a expiré. Reconnectez-vous. {reason}",
  'Saving your selection…': "Enregistrement de votre choix…",
  'Saving…': "Enregistrement…",
  'Secure account access': "Accès sécurisé au compte",
  'Secure account sign-in opens in the installed OPES Health app. This web preview stays on this screen.': "La connexion sécurisée s’ouvre dans l’application OPES Health installée. Cet aperçu web reste sur cet écran.",
  'Secure sign-in is not provisioned in this app build. Please contact OPES Health support.': "La connexion sécurisée n’est pas configurée dans cette version. Contactez l’assistance OPES Health.",
  'Self-paced': "À votre rythme",
  'Self-paced course': "Cours à votre rythme",
  'Self-paced learning': "Apprentissage à votre rythme",
  'Send reply': "Envoyer la réponse",
  'Service type': "Type de service",
  'Show unread only': "Afficher uniquement les non lues",
  'Showing unread': "Non lues affichées",
  'Sign in': "Se connecter",
  'Sign in again to open your account.': "Reconnectez-vous pour ouvrir votre compte.",
  'Sign in securely': "Connexion sécurisée",
  'Sign in to view your account.': "Connectez-vous pour voir votre compte.",
  'Sign in to your OPES account. Your role and permissions open the right workspace automatically.': "Connectez-vous à votre compte OPES. Votre rôle et vos autorisations ouvrent automatiquement le bon espace.",
  'Sign out': "Se déconnecter",
  'Sign out?': "Se déconnecter ?",
  'Sign-in could not be completed.': "La connexion n’a pas pu aboutir.",
  'Sign-in could not be completed. Try again.': "La connexion n’a pas pu aboutir. Réessayez.",
  'Specialty (optional)': "Spécialité (facultatif)",
  'Start': "Démarrer",
  'Start an application': "Commencer une candidature",
  'Start another attempt': "Nouvelle tentative",
  'Start exam': "Commencer l’examen",
  'Start review': "Commencer l’examen du dossier",
  'Steps and expected result': "Étapes et résultat attendu",
  'Study lesson': "Leçon",
  'Study lesson {index} of {total}': "Leçon {index} sur {total}",
  'Study lessons': "Leçons",
  'Study notes': "Notes d’étude",
  'Study practical lessons at your pace. Mark each lesson complete to keep your progress and earn course certificates.': "Étudiez des leçons pratiques à votre rythme. Marquez chaque leçon comme terminée pour conserver votre progression et obtenir des certificats.",
  'Study the lessons below, then mark each one complete to save your progress.': "Étudiez les leçons ci-dessous, puis marquez-les comme terminées pour enregistrer votre progression.",
  'Submit application': "Envoyer la candidature",
  'Submit exam': "Soumettre l’examen",
  'Tell us about your organization, the communities you serve and the kind of collaboration you have in mind. The partnerships team will review your application and follow up using your contact details.': "Présentez votre organisation, les communautés que vous servez et le type de collaboration envisagé. L’équipe partenariats examinera votre candidature et vous recontactera.",
  'The account ID from the API is not a numeric user ID, so the deal cannot be assigned safely.': "L’ID de compte renvoyé par l’API n’est pas numérique : l’opportunité ne peut pas être attribuée en toute sécurité.",
  'The action could not be completed.': "L’action n’a pas pu aboutir.",
  'The app checks for compatible updates when it opens. You can also check manually here.': "L’application recherche les mises à jour compatibles à l’ouverture. Vous pouvez aussi vérifier ici.",
  'The attempt expired. Start a new attempt if you still have attempts remaining.': "La tentative a expiré. Commencez-en une nouvelle s’il vous en reste.",
  'The exam could not be started.': "Impossible de commencer l’examen.",
  'The exam could not be submitted. Your saved answers are preserved.': "Impossible de soumettre l’examen. Vos réponses enregistrées sont conservées.",
  'The password request could not be completed.': "La demande de mot de passe n’a pas pu aboutir.",
  'The passwords do not match.': "Les mots de passe ne correspondent pas.",
  'The result could not be loaded.': "Impossible de charger le résultat.",
  'The signed-in account response is incomplete.': "La réponse du compte connecté est incomplète.",
  'This account does not have {scope} access.': "Ce compte ne dispose pas de l’accès {scope}.",
  'This action is not supported.': "Cette action n’est pas prise en charge.",
  'This app section is not available.': "Cette section n’est pas disponible.",
  'This assessment could not be loaded.': "Impossible de charger cette évaluation.",
  'This contact is now {status}. The list has been refreshed; review it before changing the status.': "Ce contact est maintenant « {status} ». La liste a été actualisée ; vérifiez-la avant de modifier le statut.",
  'This course has no published study lessons yet. Refresh later or choose another course.': "Ce cours n’a pas encore de leçons publiées. Actualisez plus tard ou choisissez un autre cours.",
  'This form submits through your account’s authorized OPES API.': "Ce formulaire est envoyé via l’API OPES autorisée pour votre compte.",
  'This lesson is complete and included in your course progress.': "Cette leçon est terminée et comptée dans votre progression.",
  'This lesson is not part of the selected course.': "Cette leçon ne fait pas partie du cours sélectionné.",
  'This notification could not be updated.': "Impossible de mettre à jour cette notification.",
  'This organization is not linked to your account.': "Cette organisation n’est pas liée à votre compte.",
  'This sample course could not be found.': "Ce cours d’exemple est introuvable.",
  'This study lesson could not be loaded.': "Impossible de charger cette leçon.",
  'This video link could not be opened.': "Impossible d’ouvrir ce lien vidéo.",
  'Try again': "Réessayer",
  'Try this workflow with sample information; nothing is sent to OPES.': "Essayez ce parcours avec des données d’exemple ; rien n’est envoyé à OPES.",
  'Unlimited': "Illimité",
  'Unlimited attempts': "Tentatives illimitées",
  'Untitled course': "Cours sans titre",
  'Update': "Mettre à jour",
  'Update contact status?': "Mettre à jour le statut du contact ?",
  'Update downloaded. Restarting the app…': "Mise à jour téléchargée. Redémarrage de l’application…",
  'Updating…': "Mise à jour…",
  'Use the action above to create your first item, or pull down to refresh.': "Utilisez l’action ci-dessus pour créer votre premier élément, ou tirez vers le bas pour actualiser.",
  'Validation certificate': "Certificat de validation",
  'View certificates': "Voir les certificats",
  'View certificates and awards': "Voir les certificats et distinctions",
  'View course details': "Voir les détails du cours",
  'View course progress & certificate': "Voir la progression et le certificat",
  'View customer details': "Voir les détails du client",
  'View details': "Voir les détails",
  'View quote': "Voir le devis",
  'View updated workspace': "Voir l’espace mis à jour",
  'Website (optional)': "Site web (facultatif)",
  'What service do you need?': "De quel service avez-vous besoin ?",
  'When the learning team publishes an assessment, you can take it here and review your result.': "Lorsque l’équipe pédagogique publiera une évaluation, vous pourrez la passer ici et consulter votre résultat.",
  'Workplace (optional)': "Lieu d’exercice (facultatif)",
  'Workspace': "Espace de travail",
  'Write a reply before sending.': "Rédigez une réponse avant l’envoi.",
  'You are enrolled. Your lessons and progress are ready.': "Vous êtes inscrit. Vos leçons et votre progression sont prêtes.",
  'You are using the latest available app update.': "Vous utilisez la dernière mise à jour disponible.",
  'You have used all allowed attempts. Review your result above.': "Vous avez utilisé toutes les tentatives autorisées. Consultez votre résultat ci-dessus.",
  'You scored {score}%. The pass mark is {mark}%.': "Vous avez obtenu {score} %. Le seuil de réussite est de {mark} %.",
  'You will need to sign in again to open your workspace.': "Vous devrez vous reconnecter pour ouvrir votre espace.",
  'Your account could not be created. Please try again.': "Votre compte n’a pas pu être créé. Veuillez réessayer.",
  'Your account could not be loaded. Please sign in again.': "Votre compte n’a pas pu être chargé. Veuillez vous reconnecter.",
  'Your account does not have notification read access.': "Votre compte n’a pas l’accès en lecture aux notifications.",
  'Your account does not have permission to view this workspace.': "Votre compte n’a pas l’autorisation d’afficher cet espace.",
  'Your account has no mobile workspace scopes. Contact your administrator to review role access.': "Votre compte n’a aucune autorisation d’espace mobile. Contactez votre administrateur pour vérifier vos accès.",
  'Your account was created on OPES Health. Continue with secure sign-in to open your live workspace.': "Votre compte OPES Health a été créé. Poursuivez avec la connexion sécurisée pour ouvrir votre espace.",
  'Your answer could not be saved. Retry the selected answer before continuing.': "Votre réponse n’a pas pu être enregistrée. Réessayez avant de continuer.",
  'Your changes were submitted successfully.': "Vos modifications ont bien été envoyées.",
  'Your completion is recorded. Open Certificates to see your award.': "Votre réussite est enregistrée. Ouvrez Certificats pour voir votre distinction.",
  'Your last attempt is saved. Resume it to continue where you left off.': "Votre dernière tentative est enregistrée. Reprenez-la là où vous vous êtes arrêté.",
  'Your learning': "Votre formation",
  'Your name': "Votre nom",
  'Your name is required.': "Votre nom est obligatoire.",
  'Your password has been reset. Sign in with the new password.': "Votre mot de passe a été réinitialisé. Connectez-vous avec le nouveau mot de passe.",
  'Your session ended': "Votre session est terminée",
  'Your session has expired. Sign in again to continue.': "Votre session a expiré. Reconnectez-vous pour continuer.",
  'Your workspace and available actions are determined from the current account roles and API permissions.': "Votre espace et vos actions dépendent des rôles du compte et des autorisations API.",
  'Your workspaces': "Vos espaces",
  'You’re all caught up': "Vous êtes à jour",
  'name@organization.org': "nom@organisation.org",
  '{count} attempts allowed': "{count} tentatives autorisées",
  '{count} attempts available': "{count} tentatives disponibles",
  '{count} hr': "{count} h",
  '{count} min': "{count} min",
  '{count} minutes': "{count} minutes",
  '{count} study lessons · {duration}': "{count} leçons · {duration}",
  '{count} unread notifications': "{count} notifications non lues",
  '{done} of {total} lessons complete · {progress}%': "{done} leçons terminées sur {total} · {progress} %",
  '{hours} hour estimated': "{hours} h estimée(s)",
  '{label}: {count}. Open': "{label} : {count}. Ouvrir",
  '{name} removed from this organization.': "{name} a été retiré de cette organisation.",
  '{name} will be approved as an OPES partner.': "{name} sera approuvé comme partenaire OPES.",
  '{name} will be marked completed and can no longer be started.': "{name} sera marquée comme terminée et ne pourra plus être démarrée.",
  '{name} will be marked rejected.': "{name} sera marqué comme rejeté.",
  '{name} will move to the next follow-up stage.': "{name} passera à l’étape de suivi suivante.",
  '{name} · tap to explore': "{name} · touchez pour explorer",

  'Approximate subtotal: {amount}': "Sous-total approximatif : {amount}",
  'Award level: {level}': "Niveau de distinction : {level}",
  'Certificate no. {number}': "Certificat n° {number}",
  'Correct answer: {answer}': "Bonne réponse : {answer}",
  'Demo mode · {organization} ({reference}). Fictional data; changes stay on this device and reset when you leave.': "Mode démo · {organization} ({reference}). Données fictives ; les modifications restent sur cet appareil et sont effacées à la sortie.",
  'Hello, {name}': "Bonjour, {name}",
  'Issued {date}': "Délivré le {date}",
  'Level: {level}': "Niveau : {level}",
  'Line items · {count}': "Lignes · {count}",
  'Most recent score: {score}%': "Dernier score : {score} %",
  'QUESTION {number} · 1 POINT': "QUESTION {number} · 1 POINT",
  'QUESTION {number} · {points} POINTS': "QUESTION {number} · {points} POINTS",
  'Quantity {quantity} · Unit {unit} · Line total {total}': "Quantité {quantity} · Unité {unit} · Total ligne {total}",
  'Score: {score}%': "Score : {score} %",
  'This account has read access only. Ask your administrator for the {scope} permission to submit changes.': "Ce compte est en lecture seule. Demandez à votre administrateur l’autorisation {scope} pour envoyer des modifications.",
  'Time remaining: {time}. The server enforces the deadline.': "Temps restant : {time}. Le serveur applique l’échéance.",
  'Version {version}': "Version {version}",
  'Your answer: {answer}': "Votre réponse : {answer}",
  'Your reply will be added to ticket {id}.': "Votre réponse sera ajoutée au ticket {id}.",
  '{correct} of {total} correct': "{correct} sur {total} correctes",
  '{count} questions · Pass mark {mark}%': "{count} questions · Seuil de réussite {mark} %",
  '{progress}% complete': "{progress} % terminé",

  'Sign-in uses OAuth 2.0 with PKCE. The secure OPES sign-in sheet returns you to this app after authentication.': "La connexion utilise OAuth 2.0 avec PKCE. La page de connexion sécurisée OPES vous ramène ensuite dans l’application.",

  // Language switch
  'Language': "Langue",
  'English': "English", // dynamic
  'Français': "Français", // dynamic
  'The app follows your phone’s language. Choose one here to override it.': "L’application suit la langue de votre téléphone. Choisissez-en une ici pour la remplacer.",
};
