### Principles
- A clean codebase is the result of constant good decisions at the micro level.
- Some rules are describe here.. You have an idea how code refactor needs this project:



1. Instead of writing repetitive else if statements, use an array to look up the wanted value based on the key you have.

    - The code will be cleaner & more readable and you will see understandable exceptions if something goes wrong. No half-passing edge cases.

```diff
- if ($order->product->option->type === 'pdf') {
-     $type = 'book';
- } else if ($order->product->option->type === 'epub') {
-     $type = 'book';
- } else if ($order->product->option->type === 'license') {
-     $type = 'license';
- } else if ($order->product->option->type === 'artwork') {
-     $type = 'creative';
- } else if ($order->product->option->type === 'song') {
-     $type = 'creative';
- } else if ($order->product->option->type === 'physical') {
-     $type = 'physical';
- }
-
- if ($type === 'book') {
-     $downloadable = true;
- } else if ($type === 'license') {
-     $downloadable = true;
- } else if ($type === 'creative') {
-     $downloadable = true;
- } else if ($type === 'physical') {
-     $downloadable = false;
- }

+ $type = [
+     'pdf' => 'book',
+     'epub' => 'book',
+     'license' => 'license',
+     'artwork' => 'creative',
+     'song' => 'creative',
+     'physical' => 'physical',
+ ][$order->product->option->type];
+
+ $downloadable = [
+     'book' => true,
+     'license' => true,
+     'creative' => true,
+     'physical' => false,
+ ][$type];
```

2. Try to avoid unnecessary nesting by returning a value early.
    - Too much nesting & else statements tend to make code harder to read.

```diff
- if ($notificationSent) {
-    $notify = false;
- } else {
-     if ($isActive) {
-         if ($total > 100) {
-             $notify = true;
-         } else {
-             $notify = false;
-         }
-     } else {
-         if ($canceled) {
-             $notify = true;
-         } else {
-             $notify = false;
-         }
-     }
- }
-
- return $notify;


+ if ($notificationSent) {
+     return false;
+ }
+
+ if ($isActive && $total > 100) {
+     return true;
+ }
+
+ if (! $isActive && $canceled) {
+     return true;
+ }
+
+ return false;
```

3. Don't create useless variables when you can just pass the value directly.

```diff
- public function create()
- {
-     $data = [
-         'resource' => 'campaign',
-         'generatedCode' => Str::random(8),
-     ];
-
-     return Inertia::render('Resource/Create', $data);
- }

+ public function create()
+ {
+     return Inertia::render('Resource/Create', [
+         'resource' => 'campaign',
+         'generatedCode' => Str::random(8),
+     ]);
+ }
```

4. The opposite of the previous tip. Sometimes the value comes from a complex call and as such, creating a variable improves readability & removes the need for a comment.

    - Remember that context matters & your end goal is readability.

```diff
- Visit::create([
-     'url' => $visit->url,
-     'referer' => $visit->referer,
-     'user_id' => $visit->userId,
-     'ip' => $visit->ip,
-     'timestamp' => $visit->timestamp,
- ])->conversion_goals()->attach($conversionData);

+ $visit = Visit::create([
+     'url' => $visit->url,
+     'referer' => $visit->referer,
+     'user_id' => $visit->userId,
+     'ip' => $visit->ip,
+     'timestamp' => $visit->timestamp,
+ ]);
+
+ $visit->conversion_goals()->attach($conversionData);
```

### Why I wanted to improve readability
When working with a new eloquent application, the default language of the model aligns with the database operations such as update, create, and delete. However, as the development progresses, the language of the model needs to be more specific to the problem domain.

To achieve this, additional methods are often added to the model, which are tailored to the people and problems involved. This allows for more expressive code. For example:

```php
if ($invoice->payment !== null) {
    //
}
```

While this code may make sense to us, it may not be immediately clear to others, such as colleagues from the accounts department. The use of null and not equal to comparisons can be ambiguous and require additional cognitive overhead.

To address this, we can enhance readability by introducing domain-specific methods:

```php
if ($invoice->isPaid()) {
    // go have a party...
}
```

By encapsulating the logic within a method like `isPaid()`, the intention becomes clear and self-explanatory. This improves code comprehension not only for newcomers to the project but also for experienced and up-and-coming developers.

It's important to remember that code is read more often than it is written. By prioritizing readability, we create code that is easier to understand and maintain, benefiting the entire development team.

Additionally, improving readability can help avoid the common habit of automatically adding domain-specific methods to the model without considering alternative locations. It encourages us to think critically about the best place to place such methods for better code organization and maintainability.

For example, consider different ways to handle due dates:

```php
if ($invoice->due_at->lt(now())) {

}

if ($invoice->due_at->isPast()) {

}

if ($invoice->isOverdue()) {

}
```

5. Don't think that long variable/method names are wrong. They're not. They're expressive.
- Better to call a longer method than a short one and check the docblock to understand what it does.
- Same with variables. Don't use nonsense 3-letters abbreviations.

```diff
- $ord = Order::create($data);
-
- // ...
-
- $ord->notify();

+ $order = Order::create($data);
+
+ // ...
+
+ $order->sendCreatedNotification();
```

6. [PHP](./composer.json/#L8C9-L8C23) has many great operators that can replace ugly if checks. Memorize them.

```diff
- if (! $foo) {
-     $foo = 'bar';
- }

+ $foo = $foo ?: 'bar';

---

- if (is_null($foo)) {
-     $foo = 'bar';
- }

+ $foo = $foo ?? 'bar';
+ $foo ??= 'bar';

---

- if (! isset($foo)) {
-     $foo = 'bar';
- }

+ $foo = $foo ?? 'bar';
+ $foo ??= 'bar';
```
