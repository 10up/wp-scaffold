# Project Engineering Workflow

## [Client] | [Project]

**Date:** [Date]

---

## Document Information

### Version History

| Version | Date | Author(s) | Description |
| :------ | :--- | :--------- | :---------- |
| 1.0 | [date] | [PjM, ENG] | Initial draft |

### Purpose

This document standardizes guidelines for defining engineering workflow and identifying key development resources. Understanding that workflow will vary between projects, this document identifies components that should be consistent regardless of project workflow disparities. Some sections are flagged as "optional" and usage may be determined on a case-by-case basis.

### Keeping This Document Up To Date

The **technical lead** should review and update this document upon any significant project milestone, including:

- Onboarding new engineers (back-end, front-end, or headless CMS)
- Updating the version of the platform (e.g., WordPress, Next.js)
- Prior to deployment of significant code additions or modifications
- Prior to any client demos
- Prior to live deployment

---

## Quick Links

| Resource | Link |
| :------- | :--- |
| Local Setup Instructions | [Local Setup](./local-setup.md) |
| Launch Plan Document | [link] |
| Routine Maintenance Plan | [link] |

---

## General Project Information

### Responsible Parties

> **Required** | Technical Lead Responsible
>
> It should always be clear who is ultimately responsible for ensuring the quality and security of production deployment on this project.

| Role | Details |
| :--- | :------ |
| **Project Technical Lead** | [Name, contact info] |
| **Project Director of Engineering** | [Name, contact info] |
| **Client Contact(s)** | [Name, contact info] |

### Deployment Workflow

> **Required** | Technical Lead Responsible
>
> Prominently display any information that is **critical** about the workflow. For clarity, present these as DOs and DON'Ts.

**You should ALWAYS:**
- Create new feature branches off of **main**.
- Run build scripts to compile assets before pushing.

**You should NEVER:**
- Merge the develop branch directly into your feature branches or main.
- Merge your feature branch into main without approval from the client and PjM/PL.

#### Feature Development Workflow

Every project should contain at minimum a workflow diagram or a step-by-step list detailing the production deployment process. Engineers should test and QA their own work before passing it off to code review or a QA analyst.

##### Workflow Setup Steps

| Step | Action |
| :--- | :----- |
| 1 | **Merge Request Template** — Add a merge request template to the repository. |
| 2 | **Protect main Branch** — Ensure the main branch is protected. |

##### Workflow Steps

> Technical lead to collaborate with Project Manager on the workflow below. Update this section after launch accordingly.

| Status | Action |
| :----- | :----- |
| **In Progress** | After selecting a task, engineers create a feature/task branch from **main**. Move the task from "To Do" to "In Progress" in the project management tool. |
| **Code Review** | **Engineer:** Before moving to "Code Review", self-testing must be completed on a **local or develop** environment, including functional, cross-browser, and accessibility testing if applicable. All unit tests must pass. Include clear testing instructions, acceptance criteria, links, and expected results. Create a merge request against **main** using the merge request template. **Code Reviewer:** Review the merge request for adherence to best practices. Ask questions and add comments. Ensure unit tests pass. Complete the merge request template checklist. Accepted tasks move to QA; rejected tasks return to "To Do". |
| **QA** | Assign QA to PjM, Technical Lead, or Engineer for review on the staging site. Accepted tasks move to "UAT"; rejected tasks return to "To Do" with a comment. |
| **UAT** | Assign back to the engineer. Engineer writes a comment with UAT instructions and assigns to the client. Accepted tasks move to "Ready to Merge"; rejected tasks return to "To Do". |
| **Ready for Merge** | Tasks approved on staging are ready for merge. Engineer merges the **feature branch** into **main**, resolving any merge conflicts. Move the task to "Merged for Deploy". |
| **Merged for Deploy** | Tasks are deployed when assigned "Merged for Deploy". |
| **Done** | Transition here with a resolution of "Done" from "Merged for Deploy". |

#### Code Review

Code review ensures all code meets minimum requirements for security and engineering best practices.

- Identify what code is being reviewed
- Identify which roles or individuals are responsible for the review

#### Branch Naming Conventions

- Prefixes:
  - `feature/branch-name`
  - `fix/branch-name`
  - `qa/branch-name`
  - `uat/branch-name`
- Should commit messages include a ticket number?

---

## Working Hours Expectations

Complete this table with client-specific working hours information:

| Item | Details |
| :--- | :------ |
| **Formal 24/7 support agreement?** | Yes / No (if yes, link to documentation) |
| **Client location** | [location] |
| **Client time zone** | [time zone] |
| **Time shift required?** | Yes / No |
| **Observes American holidays?** | Yes / No |
| **SLA Information** | [response time] [business hours] [time of day] [days of week] — Example: 2 business days, 8 business hours, 8 a.m.–5 p.m. ET, Monday–Friday |

---

## Technical Resources

### Hosting & Environment

> **Required** | Technical Lead Responsible

| Field | Details |
| :---- | :------ |
| **Escalation Procedure** | Request support from the resourced project technical lead. If unavailable, request guidance from your Director of Engineering. |

#### Hosting Information

| Field | Details |
| :---- | :------ |
| **Hosting Provider** | (e.g., WordPress VIP, WP Engine, Vercel, Managed Hosting) |
| **Hosting Plan Level** | (e.g., "Business Plan with CDN add-on") |
| **Contact Information** | Names, roles, email addresses, phone numbers, preferred method of contact |
| **Hosting Status Page** | [URL] |
| **In Case of Emergency** | How to get support (e.g., site down) |

#### CDN Provider

> Optional — may not apply to all projects.

| Field | Details |
| :---- | :------ |
| **CDN Provider** | (e.g., Cloudflare) |
| **CDN Plan Level** | (e.g., Pro) |
| **CDN Status Page** | [URL] |
| **CDN Contacts** | Names, roles, email, phone, preferred contact method |
| **In Case of Emergency** | Who to contact for CDN emergencies |

#### DNS

| Field | Details |
| :---- | :------ |
| **DNS Registrar** | (e.g., GoDaddy). Indicate if all domains are registered here; if not, document each unique configuration. |
| **DNS Nameservers** | Include nameservers for all domains in this project |
| **DNS Nameserver Status Page** | [URL] |
| **DNS Registration Expiration** | (obtainable via `whois`) |
| **DNS Contacts** | Include contacts for both the registrar and nameserver hosting |
| **In Case of Emergency** | Who to contact for DNS emergencies |

#### SSL

| Field | Details |
| :---- | :------ |
| **SSL Certificate Provider** | (e.g., Namecheap, Let's Encrypt) |
| **SSL Certificate Expiration Date** | Cover all certificates in use (CDN, webservers, Elasticsearch, etc.) |
| **SSL Contacts** | Who to contact to renew the SSL certificate |

### Server Information

> **Required** | Technical Lead Responsible

#### Stack Information

> Last updated: [date]

| Field | Details |
| :---- | :------ |
| **Hosting Provider** | |
| **MariaDB Version** | |
| **PHP Version** | |
| **WP Version** | |
| **Server Info** | Server OS: / Web server: / Varnish: / Object cache: |
| **CDN** | Provider: / Configuration: |
| **WP-CLI** | |
| **WP-cron** | |

#### Access Control Rules

> Last updated: [date]

| Field | Details |
| :---- | :------ |
| **General** | |
| **VPN** | |
| **WordPress** | |
| **SSH** | |
| **Plugin Installs & WordPress Updates** | |

#### Server Architecture Diagram

> Optional — Insert server architecture diagram if available.

### Server Credentials

> **Required** | Technical Lead Responsible

Link to credential storage (e.g., Teamwork notebook) containing relevant credentials such as SSH, MySQL, SFTP.

**DO NOT put credentials directly in this document.** If credentials cannot be shared via the standard tool, provide clear direction for where this information can be obtained.

### Database Export

> **Optional** | Technical Lead Responsible
>
> Last updated: [date]

| Field | Details |
| :---- | :------ |
| **Who can provide a database dump?** | |
| **Direct Link** | [link, if applicable] |

### Monitoring and Analytics

> **Required** | Technical Lead Responsible

#### Server Metrics

> Last updated: [date]

| Service | Details |
| :------ | :------ |
| **Cloudwatch / Datadog / Munin** | URL: / Account owner: / Link to credentials: |
| **New Relic Infrastructure** | URL: / Account owner: / Link to credentials: |

#### Application Performance Monitoring

> Last updated: [date]

| Service | Details |
| :------ | :------ |
| **New Relic / Datadog** | URL: / Account owner: / Link to credentials: / Is tracking enabled? |
| **Pingdom** | URL: / Account owner: / Link to credentials: |

#### Analytics

> Last updated: [date]

| Service | Details |
| :------ | :------ |
| **Google Analytics** | URL: / Account owner: / Link to credentials: |
| **Comscore** | URL: / Account owner: / Link to credentials: |

#### Front-End Performance Monitoring

Performance testing should be conducted before and after any major feature deployments to a production environment. All performance metric tests should be run against the following key pages:

| Page | URL |
| :--- | :-- |
| Homepage | [URL] |
| [Other key page] | [URL] |

#### Security

| Item | Details |
| :--- | :------ |
| **Dynamic Application Security Testing** | Is penetration testing required? Who will perform it and when? |
| **Static Code Analysis** | Will code analysis by a 3rd party or the client be required prior to launch? |
| **Data and Privacy Requirements** | Any requirements that apply to production data (e.g., CCPA, GDPR, HIPAA) |

---

## Third Party Integrations

> **Optional** | Technical Lead Responsible
>
> **Important**: All new third party integrations must be vetted and approved by the Director of Engineering to ensure they meet security and quality standards.
>
> Common integrations include fonts, feed ingestion services, social platforms, authentication services, etc.

### [Name of Third Party]

| Field | Details |
| :---- | :------ |
| **Link to credentials** | Link to credential storage. **DO NOT put credentials directly in this document.** |
| **3rd party login URL** | [URL] |
| **WP admin settings screen** | Staging: / Production: |
| **Special Setup Instructions** | Any required updates to `wp-config.php` or special instructions for local testing |
| **Description** | Any historical information useful to new engineers |

> Repeat the table above for each third party integration.
