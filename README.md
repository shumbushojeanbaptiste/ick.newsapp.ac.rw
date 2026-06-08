# www.kaminuzastar.com

A concise project README for the redesign and redevelopment of the www.kaminuzastar.com website.

## Project Overview
- Goal: Redesign and redevelop the website with a modern, responsive, newsroom-style layout.
- Preserve existing domains, SSL certificates, and hosting environment.
- Maintain SEO and existing content structure where possible.

## Key Requirements
- Responsive, mobile-first design
- Fast performance and good Core Web Vitals
- Cross-browser compatibility and basic accessibility (WCAG AA target)
- Preserve existing domain names and SSL configuration
- Simple deployment to the current hosting environment

## Deliverables
- Frontend: HTML/CSS/JavaScript (or framework agreed upon)
- Content migration plan (if needed)
- Deployment instructions for the current hosting setup
- Basic tests for responsiveness and browser compatibility

## Local Development
1. Install XAMPP (Apache + PHP + MySQL) on Windows.
2. Place the project files in `c:\xampp\htdocs\www.kaminuzastar.com`.
3. Start Apache (and MySQL if needed) using the XAMPP control panel.
4. Open the site in your browser at:

```
http://localhost/www.kaminuzastar.com
```

## Deployment Notes
- Keep existing DNS and SSL records unchanged unless migrating hosting.
- If migrating, update DNS records and reissue or transfer SSL certificates.

## Contact
- maintainer: 0788644687

## Checklist
- [ ] Support multilingual news content: Kinyarwanda, French, and English.
- [ ] Allow each news article to include related external links and references.
- [ ] Implement news workflow statuses: Draft, Pending Publication, Published, Unpublished.
- [ ] Control workflow actions through Role-Based Access Control (RBAC).
- [ ] Support embedded radio streaming links (e.g. Salus Radio).
- [ ] Organise news articles into categories: Politics, Education, Sports, Business, Health, Technology.
- [ ] Allow authorised users to delete news articles from the platform.

