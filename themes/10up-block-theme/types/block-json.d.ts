/**
 * `import metadata from './block.json'` gives `registerBlockType()` the type
 * it expects. TypeScript only falls back to this declaration because
 * `resolveJsonModule` is off in tsconfig.json; Vite still bundles the JSON.
 */
declare module '*/block.json' {
	const value: import('@wordpress/blocks').BlockConfiguration<Record<string, unknown>>;
	export default value;
}
