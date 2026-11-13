#!/usr/bin/env node
/**
 * Installs only the named packages, at the versions package-lock.json pins, and
 * links them in as ./node_modules. For CI jobs that need a few tools (stylelint
 * and its config) rather than the workspace's full dependency tree.
 *
 * Usage (from the repo root): node bin/ci/install-tools.mjs <package>...
 */
import { execFileSync } from 'node:child_process';
import fs from 'node:fs';
import os from 'node:os';
import path from 'node:path';

const { packages } = JSON.parse(fs.readFileSync('package-lock.json', 'utf8'));

const specs = process.argv.slice(2).map((name) => {
	const version = packages[`node_modules/${name}`]?.version;
	if (!version) {
		throw new Error(`${name} isn't in package-lock.json.`);
	}
	return `${name}@${version}`;
});

const prefix = path.join(process.env.RUNNER_TEMP ?? os.tmpdir(), 'ci-tools');
execFileSync(
	'npm',
	[
		'install',
		'--no-save',
		'--no-package-lock',
		'--ignore-scripts',
		'--no-audit',
		'--no-fund',
		'--prefix',
		prefix,
		...specs,
	],
	{ stdio: 'inherit' },
);
fs.symlinkSync(path.join(prefix, 'node_modules'), 'node_modules');
