# evolution
<h2>Requirements</h2>
<ul>
  <li>Node js installed</li>
  <li>npm installed</li>
  <li>PHP installed</li>
  <li>MySQL installed</li>
  <li><a href="https://getcomposer.org/download/" target="_blank">Composer</a> installed</li>
</ul>

<h2>Installation</h2>
<p>Run <code>git clone https://github.com/kinsokafor/evolution.git your-project-name</code> in your desired project location</p>
<p>Run <code>cd your-project-name</code></p>
<p>Run <code>node evolution</code></p>
<p>Run <code>composer install</code></p>
<p>Run <code>npm install</code></p>

<h2>Set up</h2>
<p>Run <code>npm run start</code> or <code>npm start</code></p>
<ul>
  <li>Get your database credentials handy (host name, user name, password, and database name)</li>
</ul>

<h2>Install Plugin</h2>
<p>Plugin installation can be done by simply running <code>npm run plugin</code>. You will be prompted to provide the name of the plugin which should be a full github name of the plugin including the author name without "https://github.com/" example "kinsokafor/eEdu" then press enter.</p>

<h2>Create New Plugin</h2>
<p>Go to https://github.com/kinsokafor/EvoPlugin and click on "use this template" then "create new repository". Name your repository (The name of your plugin) for example "ExamplePlugin". You must be consistent with naming your plugin because you will use exactly the same name and it's exact casing for creating the javaScript counterpart. Allow it for some time to clone and create your plugin files.</p>
<p>Also go to https://github.com/kinsokafor/EvoPlugin.js and repeat the same process. This time the name of your repository must be the same as the name of the plugin which you used to create the first repository then followed by .js for example "ExamplePlugin.js"</p>
<p>Then just like plugin installation, run <code>npm run --i plugin</code> provide the name of the plugin that your earlier used to create your repository. remember to format it properly including the author name for example "kinsokafor/ExamplePlugin".</p>
<p>You will be required to provide the entryURI of your plugin which will be the index of your new plugin. Get a unique name such as "example-plugin".</p>
<p>You will also be required to provide the plugin prefix. The plugin prefix is just 3 alphanumeric characters that are unique to the naming of your plugin so that it won't collide with anyother plugin used in your project. Make it as unique as possible</p>
<p>Click Enter.</p>
