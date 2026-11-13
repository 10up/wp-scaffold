/**
 * Stylelint formatter for CI: one GitHub Actions `::error` (or `::warning`)
 * workflow command per problem, so each shows up as an annotation on the PR
 * diff, followed by a plain `file:line:column rule message` list for the log.
 *
 *     stylelint --custom-formatter bin/ci/stylelint-github-formatter.cjs …
 */
const path = require('node:path');

/**
 * Escapes a value for a workflow command property or message.
 *
 * @param {string} value The raw text.
 * @returns {string} The escaped text.
 */
const escape = (value) =>
	value
		.replace(/%/g, '%25')
		.replace(/\r/g, '%0D')
		.replace(/\n/g, '%0A')
		.replace(/:/g, '%3A')
		.replace(/,/g, '%2C');

/**
 * @param {import('stylelint').LintResult[]} results Stylelint's results.
 * @returns {string} The formatted output.
 */
module.exports = function githubFormatter(results) {
	const annotations = [];
	const lines = [];
	for (const result of results) {
		const file = path.relative(process.cwd(), result.source ?? '');
		const problems = [
			...result.warnings,
			...(result.parseErrors ?? []),
			...result.invalidOptionWarnings.map((warning) => ({
				...warning,
				line: 1,
				column: 1,
				rule: 'invalid-option',
				severity: 'error',
			})),
		];
		for (const { line = 1, column = 1, rule, severity, text } of problems) {
			const level = severity === 'warning' ? 'warning' : 'error';
			annotations.push(
				`::${level} file=${escape(file)},line=${line},col=${column},title=${escape(rule)}::${escape(text)}`,
			);
			lines.push(`${file}:${line}:${column} ${rule} ${text}`);
		}
	}
	if (lines.length === 0) {
		return '';
	}
	return `${annotations.join('\n')}\n\n${lines.join('\n')}\n\n${lines.length} problem${lines.length === 1 ? '' : 's'}\n`;
};
