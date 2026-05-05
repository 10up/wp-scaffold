import type { BlockConfiguration } from '@wordpress/blocks';

declare module '*.json' {
	const value: BlockConfiguration<Record<string, unknown>>;
	export default value;
}
