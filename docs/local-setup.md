# Local Setup Documentation

## [Client] | [Project]

**Date:** [Date]

---

## Document Information

### Version History

| Version | Date | Author(s) | Description |
| :------ | :--- | :--------- | :---------- |
| 1.0 | [date] | [PM, ENG and/or SYS names] | Initial draft |

### Purpose

This document provides a standardized format for local setup documentation. It is intentionally high-level to account for the wide spectrum of projects that exist. Fill in each section with project-specific details.

---

## General Information

> **Required** | Project Lead Responsible

Provide a brief write-up of any relevant project-specific information or background, as well as links to important resources.

| Resource | Link |
| :------- | :--- |
| Engineering Workflow Document | [link] |
| Launch Plan | [link] |
| Project Management (Teamwork / Jira) | [link] |

---

## Prerequisite Checklist

> **Required** | Project Lead Responsible
>
> Provide a **comprehensive** checklist of all resources that an engineer will need in order to fully unblock them to complete local setup.
>
> It is the Technical Lead's responsibility to ensure that this list stays updated. Do not assume that an incoming engineer has any context to the specifics of this local setup. This should be a high-level list. **Do NOT include credentials in this list.**

- [ ] Repo access
- [ ] Project management access (Teamwork / Jira)
- [ ] New Relic access
- [ ] 2-factor authentication setup
- [ ] Database dump
- [ ] WordPress credentials (consider all environments)
- [ ] Required plugins
- [ ] Custom configuration
  - [ ] Webpack / Deploybot
  - [ ] Composer in theme, plugins, or MU plugins
- [ ] Any required constants
  - [ ] `JETPACK_STAGING_MODE`
  - [ ] Multisite configuration
  - [ ] [Other project-specific constants]

---

## Repeatable Setup Steps

Engineers should follow Snapshots setup steps whenever possible. In some cases, manual setup may be necessary.

### Snapshots Setup

> **Required** | Project Lead Responsible
>
> Ensure there is a recent copy of this environment available on [Snapshots](https://github.com/10up/snapshots).

**Snapshot ID:** `<ID>`

**Search:**
```bash
wp snapshots search <search_term>
```

Follow Snapshots "pull" instructions.

Once the snapshot is successfully pulled locally, ensure all personally identifiable information is scrubbed from the database using WP Scrubber.

### Manual Setup Steps

> **Required** | Project Lead Responsible
>
> Provide an ordered, step-by-step guide that new engineers can follow to achieve a functional local environment. The only assumption that should be made here is that the engineer has completed the above prerequisite checklist.

**Guidelines:**
- Be specific and include screenshots to communicate complex configurations where necessary.
- Do not detail general environment setup (e.g., setting up Local WP) unless absolutely necessary. Instead, link out to the existing [Installation Guide](./installation.md).
- The formatting for this section may vary widely between projects. This is fine, so long as the process is easy to follow and can be successfully repeated by engineers without prior context.

**Local Development URL:** `project-name.test`

> All engineers should use the same local development URL.

1. [Step 1]
2. [Step 2]
3. [Step 3]

**Important**: Manual steps should always include a reminder to scrub all personally identifiable information from the local database using WP Scrubber.

---

## Screenshot

> **Optional** | Project Lead Responsible
>
> Provide a screenshot of what the local homepage should look like upon successful setup.

<!-- Add screenshot here -->
