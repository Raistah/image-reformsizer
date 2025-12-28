import parse from 'html-react-parser';
export default function save({attributes}) {
	return parse(attributes.tag);
}
