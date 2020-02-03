/**
 * Tests the FAQ Event handler class.
 *
 * @since ???
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

import FaqEventHandler from "../faq-event-handler";
import TinyMceFaqHook from "../hooks/tiny-mce-faq-hook";

beforeEach(() => {
  global["tinymce"] = undefined;
});
test("check if the correct hook is initialized", () => {
  // Create tinymce in the global namespace, so the event handler
  // would initialize the tinymce hook.
  global["tinymce"] = "";
  const eventHandler = new FaqEventHandler();
  expect(eventHandler.getHook()).toBeInstanceOf(TinyMceFaqHook);
});

test("check if no hook condition matches, null should be returned", () => {
  const eventHandler = new FaqEventHandler();
  expect(eventHandler.getHook()).toEqual(null)
});
